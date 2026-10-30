<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\EcommerceDelivery;
use App\Models\EcommerceOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EcommerceDeliveryController extends Controller
{
    private function companyId(): int
    {
        abort_unless(auth()->user()?->company_id, 403, 'Entreprise introuvable.');
        return (int) auth()->user()->company_id;
    }

    private function ensureOwnDelivery(EcommerceDelivery $delivery): void
    {
        abort_unless((int) $delivery->company_id === $this->companyId(), 404);
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId();
        $query = EcommerceDelivery::where('company_id', $companyId)->latest();

        if ($request->filled('status') && array_key_exists($request->status, EcommerceDelivery::STATUSES)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $term = trim($request->q);
            $query->where(fn ($q) => $q
                ->where('reference', 'like', "%{$term}%")
                ->orWhere('recipient_name', 'like', "%{$term}%")
                ->orWhere('recipient_phone', 'like', "%{$term}%")
                ->orWhere('city', 'like', "%{$term}%"));
        }

        $deliveries = $query->paginate(15)->withQueryString();
        $base = EcommerceDelivery::where('company_id', $companyId);

        $stats = [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->whereIn('status', ['pending', 'preparing'])->count(),
            'shipped' => (clone $base)->where('status', 'shipped')->count(),
            'delivered' => (clone $base)->where('status', 'delivered')->count(),
        ];

        return view('company.ecommerce.deliveries.index', [
            'deliveries' => $deliveries,
            'stats' => $stats,
            'statuses' => EcommerceDelivery::STATUSES,
        ]);
    }

    public function create()
    {
        $orders = EcommerceOrder::where('company_id', $this->companyId())->latest()->get();

        return view('company.ecommerce.deliveries.create', [
            'orders' => $orders,
            'statuses' => EcommerceDelivery::STATUSES,
            'delivery' => new EcommerceDelivery(),
        ]);
    }

    public function store(Request $request)
    {
        $companyId = $this->companyId();
        $data = $this->validateDelivery($request);

        EcommerceOrder::where('company_id', $companyId)->findOrFail($data['order_id']);

        $exists = EcommerceDelivery::where('company_id', $companyId)
            ->where('order_id', $data['order_id'])
            ->whereNotIn('status', ['cancelled', 'failed'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'order_id' => 'Une livraison active existe déjà pour cette commande.',
            ]);
        }

        $data['company_id'] = $companyId;
        $data['created_by'] = auth()->id();
        $data['reference'] = $this->newReference();
        $this->applyStatusDates($data, null);

        $delivery = EcommerceDelivery::create($data);

        return redirect()->route('company.ecommerce.deliveries.show', $delivery)
            ->with('success', 'Livraison créée avec succès.');
    }

    public function show(EcommerceDelivery $delivery)
    {
        $this->ensureOwnDelivery($delivery);

        return view('company.ecommerce.deliveries.show', [
            'delivery' => $delivery,
            'statuses' => EcommerceDelivery::STATUSES,
        ]);
    }

    public function edit(EcommerceDelivery $delivery)
    {
        $this->ensureOwnDelivery($delivery);
        $orders = EcommerceOrder::where('company_id', $this->companyId())->latest()->get();

        return view('company.ecommerce.deliveries.edit', [
            'delivery' => $delivery,
            'orders' => $orders,
            'statuses' => EcommerceDelivery::STATUSES,
        ]);
    }

    public function update(Request $request, EcommerceDelivery $delivery)
    {
        $this->ensureOwnDelivery($delivery);
        $data = $this->validateDelivery($request);
        EcommerceOrder::where('company_id', $this->companyId())->findOrFail($data['order_id']);

        $this->applyStatusDates($data, $delivery->status);
        unset($data['company_id'], $data['created_by'], $data['reference']);
        $delivery->update($data);

        return redirect()->route('company.ecommerce.deliveries.show', $delivery)
            ->with('success', 'Livraison mise à jour.');
    }

    public function updateStatus(Request $request, EcommerceDelivery $delivery)
    {
        $this->ensureOwnDelivery($delivery);
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(EcommerceDelivery::STATUSES))],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $this->applyStatusDates($data, $delivery->status);
        $delivery->update($data);

        return back()->with('success', 'Statut de livraison mis à jour.');
    }

    public function destroy(EcommerceDelivery $delivery)
    {
        $this->ensureOwnDelivery($delivery);

        if ($delivery->status === 'delivered') {
            return back()->withErrors(['delivery' => 'Une livraison déjà effectuée ne peut pas être supprimée.']);
        }

        $delivery->delete();

        return redirect()->route('company.ecommerce.deliveries.index')
            ->with('success', 'Livraison supprimée.');
    }

    private function validateDelivery(Request $request): array
    {
        return $request->validate([
            'order_id' => ['required', 'integer'],
            'recipient_name' => ['required', 'string', 'max:150'],
            'recipient_phone' => ['required', 'string', 'max:40'],
            'recipient_email' => ['nullable', 'email', 'max:190'],
            'address_line' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'zone' => ['nullable', 'string', 'max:120'],
            'delivery_fee' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'carrier_name' => ['nullable', 'string', 'max:150'],
            'carrier_phone' => ['nullable', 'string', 'max:40'],
            'status' => ['required', Rule::in(array_keys(EcommerceDelivery::STATUSES))],
            'scheduled_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function applyStatusDates(array &$data, ?string $oldStatus): void
    {
        if (($data['status'] ?? null) === 'shipped' && $oldStatus !== 'shipped') {
            $data['shipped_at'] = now();
        }

        if (($data['status'] ?? null) === 'delivered' && $oldStatus !== 'delivered') {
            $data['shipped_at'] = $data['shipped_at'] ?? now();
            $data['delivered_at'] = now();
        }
    }

    private function newReference(): string
    {
        do {
            $reference = 'LIV-' . now()->format('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        } while (EcommerceDelivery::where('reference', $reference)->exists());

        return $reference;
    }
}

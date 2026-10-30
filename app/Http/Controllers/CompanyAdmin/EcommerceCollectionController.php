<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\EcommerceCollection;
use App\Models\EcommerceProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EcommerceCollectionController extends Controller
{
    private function companyId(): int
    {
        $companyId = auth()->user()?->company_id;

        abort_unless($companyId, 403, 'Entreprise introuvable.');

        return (int) $companyId;
    }

    private function findCollection(int $id): EcommerceCollection
    {
        return EcommerceCollection::query()
            ->where('company_id', $this->companyId())
            ->findOrFail($id);
    }

    private function generateSlug(
        string $name,
        int $companyId,
        ?int $ignoreId = null
    ): string {
        $base = Str::slug($name) ?: 'collection';
        $slug = $base;
        $number = 2;

        while (
            EcommerceCollection::query()
                ->where('company_id', $companyId)
                ->where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) => $query->where('id', '!=', $ignoreId)
                )
                ->exists()
        ) {
            $slug = $base . '-' . $number++;
        }

        return $slug;
    }

    private function availableProducts()
    {
        return EcommerceProduct::query()
            ->with('product')
            ->where('company_id', $this->companyId())
            ->orderBy('id', 'desc')
            ->get();
    }

    private function validatedData(Request $request): array
    {
        $companyId = $this->companyId();

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'description' => ['nullable', 'string'],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'seo_title' => ['nullable', 'string', 'max:255'],

            'seo_description' => ['nullable', 'string', 'max:2000'],

            'sort_order' => ['nullable', 'integer', 'min:0'],

            'is_active' => ['required', 'boolean'],

            'is_featured' => ['required', 'boolean'],

            'products' => ['nullable', 'array'],

            'products.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('ecommerce_products', 'id')
                    ->where('company_id', $companyId),
            ],
        ]);
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId();

        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', '');

        $query = EcommerceCollection::query()
            ->where('company_id', $companyId)
            ->withCount('products');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        } elseif ($status === 'featured') {
            $query->where('is_featured', true);
        }

        $collections = $query
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => EcommerceCollection::where('company_id', $companyId)
                ->count(),

            'active' => EcommerceCollection::where('company_id', $companyId)
                ->where('is_active', true)
                ->count(),

            'inactive' => EcommerceCollection::where('company_id', $companyId)
                ->where('is_active', false)
                ->count(),

            'featured' => EcommerceCollection::where('company_id', $companyId)
                ->where('is_featured', true)
                ->count(),
        ];

        return view(
            'company.ecommerce.collections.index',
            compact('collections', 'stats', 'search', 'status')
        );
    }

    public function create()
    {
        $products = $this->availableProducts();

        return view(
            'company.ecommerce.collections.create',
            compact('products')
        );
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $companyId = $this->companyId();

        $productIds = $data['products'] ?? [];

        unset($data['products'], $data['image']);

        $data['company_id'] = $companyId;
        $data['slug'] = $this->generateSlug(
            $data['name'],
            $companyId
        );

        $data['sort_order'] = $data['sort_order'] ?? 0;

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('ecommerce/collections', 'public');

            $data['image'] = $imagePath;
        }

        try {
            DB::transaction(function () use ($data, $productIds) {
                $collection = EcommerceCollection::create($data);

                $collection->products()->sync($productIds);
            });
        } catch (\Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        return redirect()
            ->route('company.ecommerce.collections.index')
            ->with('success', 'Collection créée avec succès.');
    }

    public function edit(int $id)
    {
        $collection = $this->findCollection($id);

        $collection->load('products');

        $products = $this->availableProducts();

        $selectedProducts = $collection->products
            ->pluck('id')
            ->all();

        return view(
            'company.ecommerce.collections.edit',
            compact('collection', 'products', 'selectedProducts')
        );
    }

    public function update(Request $request, int $id)
    {
        $collection = $this->findCollection($id);

        $data = $this->validatedData($request);

        $productIds = $data['products'] ?? [];

        unset($data['products'], $data['image']);

        if ($collection->name !== $data['name']) {
            $data['slug'] = $this->generateSlug(
                $data['name'],
                $this->companyId(),
                $collection->id
            );
        }

        $data['sort_order'] = $data['sort_order'] ?? 0;

        $oldImage = $collection->image;
        $newImage = null;

        if ($request->hasFile('image')) {
            $newImage = $request->file('image')
                ->store('ecommerce/collections', 'public');

            $data['image'] = $newImage;
        }

        try {
            DB::transaction(function () use (
                $collection,
                $data,
                $productIds
            ) {
                $collection->update($data);

                $collection->products()->sync($productIds);
            });
        } catch (\Throwable $exception) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            throw $exception;
        }

        if ($newImage && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('company.ecommerce.collections.index')
            ->with('success', 'Collection modifiée avec succès.');
    }

    public function toggleStatus(int $id)
    {
        $collection = $this->findCollection($id);

        $collection->update([
            'is_active' => ! $collection->is_active,
        ]);

        return back()->with(
            'success',
            'Statut de la collection mis à jour.'
        );
    }

    public function destroy(int $id)
    {
        $collection = $this->findCollection($id);

        $image = $collection->image;

        DB::transaction(function () use ($collection) {
            $collection->products()->detach();
            $collection->delete();
        });

        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return redirect()
            ->route('company.ecommerce.collections.index')
            ->with('success', 'Collection supprimée avec succès.');
    }
}

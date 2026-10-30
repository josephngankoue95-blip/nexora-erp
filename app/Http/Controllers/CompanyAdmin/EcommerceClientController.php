<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\EcommerceOrder;
use Illuminate\Http\Request;

class EcommerceClientController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $company = auth()->user()->company;

        $query = Client::query()
            ->where('company_id', $company->id)
            ->whereHas('ecommerceOrders')
            ->withCount('ecommerceOrders')
            ->withSum('ecommerceOrders', 'total')
            ->withMax('ecommerceOrders', 'created_at')
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clients = $query
            ->paginate(15)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES
        |--------------------------------------------------------------------------
        */

        $baseQuery = Client::query()
            ->where('company_id', $company->id)
            ->whereHas('ecommerceOrders');

        $stats = [
            'total' => (clone $baseQuery)->count(),

            'orders' => EcommerceOrder::query()
                ->where('company_id', $company->id)
                ->count(),

            'revenue' => EcommerceOrder::query()
                ->where('company_id', $company->id)
                ->whereNotIn('status', ['cancelled'])
                ->sum('total'),

            'average' => EcommerceOrder::query()
                ->where('company_id', $company->id)
                ->whereNotIn('status', ['cancelled'])
                ->avg('total') ?? 0,
        ];

        return view(
            'company.ecommerce.clients.index',
            compact('clients', 'stats')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(Client $client)
    {
        $this->authorizeCompany($client);

        /*
        |--------------------------------------------------------------------------
        | COMMANDES DU CLIENT
        |--------------------------------------------------------------------------
        */

        $orders = EcommerceOrder::query()
            ->where('company_id', $client->company_id)
            ->where('client_id', $client->id)
            ->with('items')
            ->latest()
            ->paginate(10);


        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES CLIENT
        |--------------------------------------------------------------------------
        */

        $orderQuery = EcommerceOrder::query()
            ->where('company_id', $client->company_id)
            ->where('client_id', $client->id);

        $stats = [
            'orders' => (clone $orderQuery)->count(),

            'completed_orders' => (clone $orderQuery)
                ->where('status', 'delivered')
                ->count(),

            'total_spent' => (clone $orderQuery)
                ->whereNotIn('status', ['cancelled'])
                ->sum('total'),

            'average_order' => (clone $orderQuery)
                ->whereNotIn('status', ['cancelled'])
                ->avg('total') ?? 0,
        ];

        return view(
            'company.ecommerce.clients.show',
            compact('client', 'orders', 'stats')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ISOLATION ENTREPRISE
    |--------------------------------------------------------------------------
    */

    private function authorizeCompany(Client $client): void
    {
        if (
            !$client->company_id ||
            $client->company_id !== auth()->user()->company_id
        ) {
            abort(404);
        }
    }
}
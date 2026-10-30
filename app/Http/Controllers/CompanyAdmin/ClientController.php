<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Liste des clients
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Client::where(
            'company_id',
            $companyId
        );

        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('tax_number', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | STATUT
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            if ($request->status === 'active') {

                $query->where('is_active', true);

            } elseif ($request->status === 'inactive') {

                $query->where('is_active', false);
            }
        }

        $clients = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES
        |--------------------------------------------------------------------------
        */

        $totalClients = Client::where(
            'company_id',
            $companyId
        )->count();

        $activeClients = Client::where(
            'company_id',
            $companyId
        )
            ->where('is_active', true)
            ->count();

        $inactiveClients = Client::where(
            'company_id',
            $companyId
        )
            ->where('is_active', false)
            ->count();


        return view(
            'company.clients.index',
            compact(
                'clients',
                'totalClients',
                'activeClients',
                'inactiveClients'
            )
        );
    }


    /**
     * Formulaire création
     */
    public function create()
    {
        return view('company.clients.create');
    }


    /**
     * Enregistrement
     */
    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:255',
            ],

            'country' => [
                'required',
                'string',
                'max:255',
            ],

            'tax_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

        ]);

        $validated['company_id'] = $companyId;

        Client::create($validated);

        return redirect()
            ->route('company.clients.index')
            ->with(
                'success',
                'Client ajouté avec succès.'
            );
    }


    /**
     * Fiche client
     */
    public function show($id)
    {
        $companyId = auth()->user()->company_id;

        $client = Client::where(
            'company_id',
            $companyId
        )
            ->findOrFail($id);

        return view(
            'company.clients.show',
            compact('client')
        );
    }


    /**
     * Modification
     */
    public function edit($id)
    {
        $companyId = auth()->user()->company_id;

        $client = Client::where(
            'company_id',
            $companyId
        )
            ->findOrFail($id);

        return view(
            'company.clients.edit',
            compact('client')
        );
    }


    /**
     * Mise à jour
     */
    public function update(
        Request $request,
        $id
    ) {

        $companyId = auth()->user()->company_id;

        $client = Client::where(
            'company_id',
            $companyId
        )
            ->findOrFail($id);


        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:255',
            ],

            'country' => [
                'required',
                'string',
                'max:255',
            ],

            'tax_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

        ]);


        $client->update($validated);


        return redirect()
            ->route('company.clients.index')
            ->with(
                'success',
                'Client modifié avec succès.'
            );
    }


    /**
     * Suppression
     */
    public function destroy($id)
    {
        $companyId = auth()->user()->company_id;

        $client = Client::where(
            'company_id',
            $companyId
        )
            ->findOrFail($id);

        $client->delete();

        return redirect()
            ->route('company.clients.index')
            ->with(
                'success',
                'Client supprimé avec succès.'
            );
    }
}
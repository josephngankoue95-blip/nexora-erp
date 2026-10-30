<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Liste des utilisateurs.
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->with('company')
            ->where('role', '!=', 'super_admin');

        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRE ENTREPRISE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('company_id')) {

            $query->where(
                'company_id',
                $request->company_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRE RÔLE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('role')) {

            $query->where(
                'role',
                $request->role
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRE STATUT
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            if ($request->status === 'active') {

                $query->where('is_active', true);

            } elseif ($request->status === 'inactive') {

                $query->where('is_active', false);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TRI
        |--------------------------------------------------------------------------
        */

        $users = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | DONNÉES DES FILTRES
        |--------------------------------------------------------------------------
        */

        $companies = Company::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        $roles = [
            'companyadmin' => 'Administrateur entreprise',
            'director' => 'Directeur',
            'manager' => 'Manager',
            'seller' => 'Vendeur',
            'cashier' => 'Caissier',
            'stock_manager' => 'Gestionnaire de stock',
            'accountant' => 'Comptable',
            'employee' => 'Employé',
        ];

        return view(
            'superadmin.users.index',
            compact(
                'users',
                'companies',
                'roles'
            )
        );
    }


    /**
     * Formulaire de création.
     */
    public function create()
    {
        $companies = Company::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        $roles = [
            'companyadmin' => 'Administrateur entreprise',
            'director' => 'Directeur',
            'manager' => 'Manager',
            'seller' => 'Vendeur',
            'cashier' => 'Caissier',
            'stock_manager' => 'Gestionnaire de stock',
            'accountant' => 'Comptable',
            'employee' => 'Employé',
        ];

        return view(
            'superadmin.users.create',
            compact(
                'companies',
                'roles'
            )
        );
    }


    /**
     * Enregistre un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'role' => [
                'required',
                Rule::in([
                    'companyadmin',
                    'director',
                    'manager',
                    'seller',
                    'cashier',
                    'stock_manager',
                    'accountant',
                    'employee',
                ]),
            ],

            'company_id' => [
                'required',
                'integer',
                'exists:companies,id',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | VÉRIFICATION ENTREPRISE ACTIVE
        |--------------------------------------------------------------------------
        */

        $company = Company::findOrFail(
            $validated['company_id']
        );

        if (!$company->is_active) {

            return back()
                ->withInput()
                ->withErrors([
                    'company_id' =>
                        'Cette entreprise est actuellement inactive.'
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CRÉATION
        |--------------------------------------------------------------------------
        */

        User::create([

            'name' => $validated['name'],

            'email' => $validated['email'],

            'password' => Hash::make(
                $validated['password']
            ),

            'role' => $validated['role'],

            'company_id' => $validated['company_id'],

            'is_active' =>
                $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('superadmin.users.index')
            ->with(
                'success',
                'Utilisateur créé avec succès.'
            );
    }


    /**
     * Affiche un utilisateur.
     */
    public function show(User $user)
    {
        $user->load('company');

        return view(
            'superadmin.users.show',
            compact('user')
        );
    }


    /**
     * Formulaire de modification.
     */
    public function edit(User $user)
    {
        $companies = Company::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        $roles = [
            'companyadmin' => 'Administrateur entreprise',
            'director' => 'Directeur',
            'manager' => 'Manager',
            'seller' => 'Vendeur',
            'cashier' => 'Caissier',
            'stock_manager' => 'Gestionnaire de stock',
            'accountant' => 'Comptable',
            'employee' => 'Employé',
        ];

        return view(
            'superadmin.users.edit',
            compact(
                'user',
                'companies',
                'roles'
            )
        );
    }


    /**
     * Met à jour un utilisateur.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'role' => [
                'required',
                Rule::in([
                    'companyadmin',
                    'director',
                    'manager',
                    'seller',
                    'cashier',
                    'stock_manager',
                    'accountant',
                    'employee',
                ]),
            ],

            'company_id' => [
                'required',
                'integer',
                'exists:companies,id',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | ENTREPRISE
        |--------------------------------------------------------------------------
        */

        $company = Company::findOrFail(
            $validated['company_id']
        );

        if (!$company->is_active) {

            return back()
                ->withInput()
                ->withErrors([
                    'company_id' =>
                        'Cette entreprise est actuellement inactive.'
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | DONNÉES
        |--------------------------------------------------------------------------
        */

        $user->name = $validated['name'];

        $user->email = $validated['email'];

        $user->role = $validated['role'];

        $user->company_id = $validated['company_id'];

        $user->is_active =
            $request->boolean('is_active');

        /*
        |--------------------------------------------------------------------------
        | MOT DE PASSE
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['password'])) {

            $user->password =
                Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route(
                'superadmin.users.index'
            )
            ->with(
                'success',
                'Utilisateur modifié avec succès.'
            );
    }


    /**
     * Active / désactive un utilisateur.
     */
    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;

        $user->save();

        return back()->with(
            'success',
            $user->is_active
                ? 'Utilisateur activé avec succès.'
                : 'Utilisateur désactivé avec succès.'
        );
    }


    /**
     * Supprime un utilisateur.
     */
    public function destroy(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | PROTECTION SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'super_admin') {

            return back()->with(
                'error',
                'Un Super Admin ne peut pas être supprimé depuis cette interface.'
            );
        }

        $user->delete();

        return redirect()
            ->route(
                'superadmin.users.index'
            )
            ->with(
                'success',
                'Utilisateur supprimé avec succès.'
            );
    }
}
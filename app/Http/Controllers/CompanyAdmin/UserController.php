<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
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
        $companyId = auth()->user()->company_id;

        $query = User::where('company_id', $companyId)
            ->where('role', '!=', 'super_admin');

        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRE PAR RÔLE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('role')) {

            $query->where('role', $request->role);
        }

        $users = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES
        |--------------------------------------------------------------------------
        */

        $totalUsers = User::where('company_id', $companyId)
            ->where('role', '!=', 'super_admin')
            ->count();

        $activeUsers = User::where('company_id', $companyId)
            ->where('role', '!=', 'super_admin')
            ->where('is_active', true)
            ->count();

        $inactiveUsers = $totalUsers - $activeUsers;

        return view(
            'company.users.index',
            compact(
                'users',
                'totalUsers',
                'activeUsers',
                'inactiveUsers'
            )
        );
    }


    /**
     * Formulaire de création.
     */
    public function create()
    {
        /*
        |--------------------------------------------------------------------------
        | PERMISSION
        |--------------------------------------------------------------------------
        */

        abort_unless(
            auth()->user()->hasPermission('users.create'),
            403
        );

        return view('company.users.create');
    }


    /**
     * Enregistrement.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | PERMISSION
        |--------------------------------------------------------------------------
        */

        abort_unless(
            auth()->user()->hasPermission('users.create'),
            403
        );

        $companyId = auth()->user()->company_id;

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'role' => [
                'required',
                Rule::in([
                    'director',
                    'manager',
                    'seller',
                    'cashier',
                    'stock_manager',
                    'accountant',
                    'employee',
                ]),
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

        ]);

        User::create([

            'name' => $validated['name'],

            'email' => $validated['email'],

            'password' => Hash::make(
                $validated['password']
            ),

            'role' => $validated['role'],

            'company_id' => $companyId,

            'is_active' => true,

        ]);

        return redirect()
            ->route('company.users.index')
            ->with(
                'success',
                'Utilisateur créé avec succès.'
            );
    }


    /**
     * Formulaire de modification.
     */
    public function edit(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | PERMISSION
        |--------------------------------------------------------------------------
        */

        abort_unless(
            auth()->user()->hasPermission('users.edit'),
            403
        );

        $companyId = auth()->user()->company_id;

        $user = User::where('company_id', $companyId)
            ->where('role', '!=', 'super_admin')
            ->findOrFail($user->id);

        return view(
            'company.users.edit',
            compact('user')
        );
    }


    /**
     * Modification.
     */
    public function update(
        Request $request,
        User $user
    ) {
        /*
        |--------------------------------------------------------------------------
        | PERMISSION
        |--------------------------------------------------------------------------
        */

        abort_unless(
            auth()->user()->hasPermission('users.edit'),
            403
        );

        $companyId = auth()->user()->company_id;

        $user = User::where('company_id', $companyId)
            ->where('role', '!=', 'super_admin')
            ->findOrFail($user->id);

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'role' => [
                'required',
                Rule::in([
                    'director',
                    'manager',
                    'seller',
                    'cashier',
                    'stock_manager',
                    'accountant',
                    'employee',
                ]),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

        ]);

        $user->name = $validated['name'];

        $user->email = $validated['email'];

        $user->role = $validated['role'];

        if (!empty($validated['password'])) {

            $user->password = Hash::make(
                $validated['password']
            );
        }

        $user->save();

        return redirect()
            ->route('company.users.index')
            ->with(
                'success',
                'Utilisateur modifié avec succès.'
            );
    }


    /**
     * Activation / désactivation.
     */
    public function toggleStatus(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | PERMISSION
        |--------------------------------------------------------------------------
        */

        abort_unless(
            auth()->user()->hasPermission('users.edit'),
            403
        );

        $companyId = auth()->user()->company_id;

        $user = User::where('company_id', $companyId)
            ->where('role', '!=', 'super_admin')
            ->findOrFail($user->id);

        /*
        |--------------------------------------------------------------------------
        | EMPÊCHER L'AUTO-DÉSACTIVATION
        |--------------------------------------------------------------------------
        */

        if ($user->id === auth()->id()) {

            return back()->withErrors([
                'user' =>
                    'Vous ne pouvez pas désactiver votre propre compte.',
            ]);
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        return back()->with(
            'success',
            $user->is_active
                ? 'Utilisateur activé.'
                : 'Utilisateur désactivé.'
        );
    }


    /**
     * Suppression.
     */
    public function destroy(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | PERMISSION
        |--------------------------------------------------------------------------
        */

        abort_unless(
            auth()->user()->hasPermission('users.delete'),
            403
        );

        $companyId = auth()->user()->company_id;

        $user = User::where('company_id', $companyId)
            ->where('role', '!=', 'super_admin')
            ->findOrFail($user->id);

        /*
        |--------------------------------------------------------------------------
        | EMPÊCHER L'AUTO-SUPPRESSION
        |--------------------------------------------------------------------------
        */

        if ($user->id === auth()->id()) {

            return back()->withErrors([
                'user' =>
                    'Vous ne pouvez pas supprimer votre propre compte.',
            ]);
        }

        $user->delete();

        return redirect()
            ->route('company.users.index')
            ->with(
                'success',
                'Utilisateur supprimé avec succès.'
            );
    }
}
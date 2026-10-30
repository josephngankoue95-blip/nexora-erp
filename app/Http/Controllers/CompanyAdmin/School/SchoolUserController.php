<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SchoolUserController extends Controller
{
    private const ROLES = [
        'companyadmin' => 'Administrateur principal',
        'school_admin' => 'Administrateur scolaire',
        'school_principal' => 'Proviseur',
        'school_director' => 'Directeur',
        'school_teacher' => 'Enseignant',
        'school_bursar' => 'Intendant',
        'school_supervisor' => 'Surveillant général',
        'school_sector_supervisor' => 'Surveillant de secteur',
        'school_parent' => 'Parent',
        'school_librarian' => 'Bibliothécaire',
    ];

    private function companyId(Request $request): int
    {
        $user = $request->user();
        abort_unless($user && $user->company_id, 403);
        abort_unless(in_array($user->role, ['companyadmin', 'school_admin'], true), 403);
        return (int) $user->company_id;
    }

    private function scopedUser(Request $request, User $user): User
    {
        abort_unless((int) $user->company_id === $this->companyId($request), 404);
        abort_unless(array_key_exists($user->role, self::ROLES), 404);
        abort_if($user->role === 'companyadmin', 403, 'Le compte administrateur principal est protégé.');
        return $user;
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId($request);
        $query = User::query()->where('company_id', $companyId)->whereIn('role', array_keys(self::ROLES));
        if ($request->filled('q')) {
            $term = trim((string) $request->query('q'));
            $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
        }
        if ($request->filled('role') && array_key_exists($request->query('role'), self::ROLES)) {
            $query->where('role', $request->query('role'));
        }
        if ($request->query('status') === 'active') $query->where('is_active', true);
        if ($request->query('status') === 'inactive') $query->where('is_active', false);
        $users = $query->orderBy('name')->paginate(20)->withQueryString();
        return view('company.school.users.index', ['users' => $users, 'roles' => self::ROLES]);
    }

    public function create(Request $request)
    {
        $this->companyId($request);
        return view('company.school.users.form', ['roles' => array_diff_key(self::ROLES, ['companyadmin' => true]), 'schoolUser' => null]);
    }

    public function store(Request $request)
    {
        $companyId = $this->companyId($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'role' => ['required', Rule::in(array_keys(array_diff_key(self::ROLES, ['companyadmin' => true])))],
            'password' => ['required', 'confirmed', Password::defaults()],
            'is_active' => ['required', 'boolean'],
        ]);
        $data['company_id'] = $companyId;
        $data['password'] = Hash::make($data['password']);
        User::create($data);
        return redirect()->route('company.school.users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(Request $request, User $user)
    {
        $this->scopedUser($request, $user);
        return view('company.school.users.form', ['roles' => array_diff_key(self::ROLES, ['companyadmin' => true]), 'schoolUser' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $this->scopedUser($request, $user);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(array_keys(array_diff_key(self::ROLES, ['companyadmin' => true])))],
            'is_active' => ['required', 'boolean'],
        ]);
        if ($user->is($request->user()) && (!$data['is_active'] || $data['role'] !== $user->role)) {
            return back()->withErrors(['role' => 'Vous ne pouvez pas désactiver ou changer votre propre rôle.'])->withInput();
        }
        $user->update($data);
        return redirect()->route('company.school.users.index')->with('success', 'Utilisateur modifié avec succès.');
    }

    public function toggle(Request $request, User $user)
    {
        $this->scopedUser($request, $user);
        abort_if($user->is($request->user()), 403, 'Impossible de désactiver votre propre compte.');
        $user->update(['is_active' => ! $user->is_active]);
        return back()->with('success', 'Statut du compte mis à jour.');
    }
}

@extends('layouts.school')
@section('title', 'Utilisateurs scolaires')
@section('page-title', 'Utilisateurs')
@section('page-description', 'Gestion des utilisateurs de votre établissement')
@section('content')
<div class="school-users-page">
    <header class="school-users-header">
        <div><h2>Utilisateurs</h2><p>Gérez les comptes et les rôles de votre établissement.</p></div>
        <a class="school-users-btn primary" href="{{ route('company.school.users.create') }}">+ Nouvel utilisateur</a>
    </header>
    @if(session('success'))<div class="school-users-notice" role="status">{{ session('success') }}</div>@endif
    <section class="school-users-panel">
        <form method="GET" action="{{ route('company.school.users.index') }}" class="school-users-filters">
            <label>Rechercher<input type="search" name="q" value="{{ request('q') }}" placeholder="Nom ou adresse email"></label>
            <label>Rôle<select name="role"><option value="">Tous les rôles</option>@foreach($roles as $value => $label)<option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label>Statut<select name="status"><option value="">Tous les statuts</option><option value="active" @selected(request('status') === 'active')>Actifs</option><option value="inactive" @selected(request('status') === 'inactive')>Désactivés</option></select></label>
            <button class="school-users-btn primary" type="submit">Filtrer</button>
            <a class="school-users-btn secondary" href="{{ route('company.school.users.index') }}">Réinitialiser</a>
        </form>
    </section>
    <section class="school-users-panel">
        <h3>Répertoire des comptes <small>({{ $users->total() }})</small></h3>
        <div class="school-users-table-wrap"><table><thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Statut</th><th>Actions</th></tr></thead><tbody>
        @forelse($users as $user)
        <tr><td><strong>{{ $user->name }}</strong></td><td>{{ $user->email }}</td><td>{{ $roles[$user->role] ?? $user->role }}</td><td><span class="school-users-status {{ $user->is_active ? 'active' : 'inactive' }}">{{ $user->is_active ? 'Actif' : 'Désactivé' }}</span></td><td>
            @if($user->role === 'companyadmin')
                <span class="school-users-status protected" title="Compte administrateur principal protégé">🔒 Compte protégé</span>
            @else
                <div class="school-users-actions">
                    <a class="school-users-btn secondary" href="{{ route('company.school.users.edit', $user) }}">Modifier</a>
                    <form method="POST" action="{{ route('company.school.users.toggle', $user) }}" onsubmit="return confirm('Confirmer le changement de statut ?')">
                        @csrf @method('PATCH')
                        <button class="school-users-btn secondary" type="submit" @disabled($user->is(auth()->user()))>{{ $user->is_active ? 'Désactiver' : 'Activer' }}</button>
                    </form>
                </div>
            @endif
        </td></tr>
        @empty<tr><td colspan="5" class="school-users-empty">Aucun utilisateur trouvé.</td></tr>@endforelse
        </tbody></table></div>
        <div class="school-users-pagination">{{ $users->links() }}</div>
    </section>
</div>
<style>
.school-users-page{max-width:1380px;margin:auto;color:#1e293b}.school-users-header{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:22px}.school-users-header h2{font-size:26px;margin:0}.school-users-header p{color:#64748b;margin:5px 0}.school-users-panel{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;margin-bottom:18px}.school-users-panel h3{margin:0 0 18px}.school-users-panel small{color:#64748b}.school-users-filters{display:flex;align-items:end;gap:12px;flex-wrap:wrap}.school-users-filters label{display:flex;flex-direction:column;gap:6px;font-size:12px;font-weight:700;flex:1;min-width:170px}.school-users-filters input,.school-users-filters select{width:100%;min-height:42px;padding:10px;border:1px solid #cbd5e1;border-radius:9px;background:#fff}.school-users-btn{display:inline-flex;align-items:center;justify-content:center;padding:10px 13px;border-radius:9px;text-decoration:none;font-size:13px;font-weight:700;border:1px solid #cbd5e1;cursor:pointer;white-space:nowrap}.school-users-btn.primary{background:#4f46e5;color:white;border-color:#4f46e5}.school-users-btn.secondary{background:#fff;color:#334155}.school-users-btn:disabled{opacity:.45;cursor:not-allowed}.school-users-table-wrap{overflow-x:auto}.school-users-panel table{border-collapse:collapse;width:100%;min-width:780px}.school-users-panel th,.school-users-panel td{padding:13px;text-align:left;border-bottom:1px solid #e2e8f0;font-size:13px}.school-users-panel th{background:#f8fafc;color:#64748b}.school-users-actions{display:flex;gap:7px}.school-users-actions form{margin:0}.school-users-status{display:inline-block;padding:5px 9px;border-radius:99px;font-weight:700;font-size:11px}.school-users-status.active{color:#166534;background:#dcfce7}.school-users-status.protected{color:#4338ca;background:#eef2ff}.school-users-status.inactive{color:#9f1239;background:#ffe4e6}.school-users-notice{padding:13px;background:#ecfdf5;color:#065f46;border-radius:10px;margin-bottom:15px}.school-users-empty{text-align:center;color:#64748b}.school-users-pagination{margin-top:18px}@media(max-width:650px){.school-users-panel{padding:14px}.school-users-header h2{font-size:22px}.school-users-filters label{flex-basis:100%}.school-users-filters>.school-users-btn{flex:1}}
</style>
@endsection

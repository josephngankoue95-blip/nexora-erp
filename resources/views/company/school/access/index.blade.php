@extends('layouts.school')
@section('title', 'Gestion des accès')
@section('page-title', 'Gestion des accès')
@section('page-description', 'Comptes scolaires et réinitialisation sécurisée')
@section('content')
<div class="school-card" style="margin-bottom:18px">
    <h2>Exporter les comptes scolaires</h2>
    <p>Exports administratifs : nom, email, rôle, statut. Aucun mot de passe.</p>
    <a href="{{ route('company.school.access.export.excel') }}">Télécharger Excel (.xlsx)</a>
    &nbsp; · &nbsp;
    <a href="{{ route('company.school.access.export.pdf') }}">Télécharger PDF</a>
</div>
<div class="school-card">
    <h2>Accès des utilisateurs</h2>
    <p>Les mots de passe ne sont jamais affichés. Un lien personnel est envoyé par email.</p>
    <form method="GET" action="{{ route('company.school.access.index') }}" style="display:flex;gap:10px;margin:16px 0;flex-wrap:wrap">
        <input name="q" value="{{ request('q') }}" placeholder="Nom ou email" aria-label="Rechercher" style="padding:10px;border:1px solid #ddd;border-radius:8px;flex:1;min-width:180px">
        <button type="submit" style="padding:10px 16px">Rechercher</button>
    </form>
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;text-align:left">
            <thead><tr><th style="padding:12px">Utilisateur</th><th>Rôle</th><th>Statut</th><th>Action</th></tr></thead>
            <tbody>
                @forelse($users as $schoolAccount)
                    <tr style="border-top:1px solid #e5e7eb">
                        <td style="padding:12px"><strong>{{ $schoolAccount->name }}</strong><div>{{ $schoolAccount->email }}</div></td>
                        <td>{{ $roles[$schoolAccount->role] ?? $schoolAccount->role }}</td>
                        <td>{{ $schoolAccount->is_active ? 'Actif' : 'Inactif' }}</td>
                        <td style="padding:12px">
                            @if($schoolAccount->role === 'companyadmin')
                                <span>Compte protégé</span>
                            @elseif(!$schoolAccount->is_active)
                                <span>Compte inactif</span>
                            @else
                                <form method="POST" action="{{ route('company.school.access.reset-link', $schoolAccount) }}" onsubmit="return confirm('Envoyer un lien de réinitialisation à cet utilisateur ?')">
                                    @csrf
                                    <button type="submit">Envoyer un lien</button>
                                </form>
                                <form method="POST" action="{{ route('company.school.access.sms', $schoolAccount) }}" style="margin-top:8px">
                                    @csrf
                                    <label for="phone-{{ $schoolAccount->id }}">Numéro international (ex. +2376...)</label>
                                    <input id="phone-{{ $schoolAccount->id }}" name="phone" type="tel" pattern="\+[1-9][0-9]{7,14}" placeholder="+2376XXXXXXXX" required autocomplete="off">
                                    <button type="submit" onclick="return confirm('Envoyer un lien personnel de réinitialisation par SMS ?')">Envoyer SMS</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="padding:16px">Aucun compte trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px">{{ $users->links() }}</div>
</div>
<div class="school-card" style="margin-top:20px">
    <h2>Historique des opérations</h2>
    <div style="overflow-x:auto">
        <table style="width:100%;text-align:left;border-collapse:collapse">
            <thead><tr><th>Date</th><th>Auteur (ID)</th><th>Compte (ID)</th><th>Action</th><th>Résultat</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                    <tr style="border-top:1px solid #e5e7eb"><td style="padding:10px">{{ $log->created_at }}</td><td>{{ $log->actor_user_id }}</td><td>{{ $log->target_user_id }}</td><td>{{ $log->action }}</td><td>{{ $log->result }}</td></tr>
                @empty
                    <tr><td colspan="5" style="padding:12px">Aucune opération enregistrée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

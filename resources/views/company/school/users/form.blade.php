@extends('layouts.school')
@section('title', $schoolUser ? 'Modifier un utilisateur' : 'Nouvel utilisateur')
@section('page-title', $schoolUser ? 'Modifier un utilisateur' : 'Nouvel utilisateur')
@section('content')
<div class="school-user-form-page"><div class="school-user-form-card">
<h2>{{ $schoolUser ? 'Modifier le compte' : 'Créer un compte utilisateur' }}</h2>
<p>Les utilisateurs appartiennent exclusivement à votre établissement.</p>
@if($errors->any())<div class="school-user-errors"><strong>Veuillez corriger les erreurs :</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ $schoolUser ? route('company.school.users.update', $schoolUser) : route('company.school.users.store') }}">
@csrf @if($schoolUser) @method('PUT') @endif
<div class="school-user-form-grid">
<label>Nom complet <span>*</span><input name="name" type="text" maxlength="255" required value="{{ old('name', $schoolUser?->name) }}"></label>
<label>Adresse email <span>*</span><input name="email" type="email" maxlength="255" required value="{{ old('email', $schoolUser?->email) }}"></label>
<label>Rôle scolaire <span>*</span><select name="role" required><option value="">Sélectionnez un rôle</option>@foreach($roles as $value => $label)<option value="{{ $value }}" @selected(old('role', $schoolUser?->role) === $value)>{{ $label }}</option>@endforeach</select></label>
<label>Statut <span>*</span><select name="is_active"><option value="1" @selected((string) old('is_active', $schoolUser?->is_active ?? 1) === '1')>Actif</option><option value="0" @selected((string) old('is_active', $schoolUser?->is_active ?? 1) === '0')>Désactivé</option></select></label>
@if(!$schoolUser)
<label>Mot de passe initial <span>*</span><input name="password" type="password" required autocomplete="new-password"></label>
<label>Confirmer le mot de passe <span>*</span><input name="password_confirmation" type="password" required autocomplete="new-password"></label>
@endif
</div>
@if($schoolUser)<p class="school-user-help">Le mot de passe se réinitialisera depuis le futur module Gestion des accès.</p>@else<p class="school-user-help">Utilisez un mot de passe provisoire fort. Ne l'envoyez pas par email en clair.</p>@endif
<div class="school-user-form-actions"><a href="{{ route('company.school.users.index') }}">Annuler</a><button type="submit">{{ $schoolUser ? 'Enregistrer les modifications' : 'Créer le compte' }}</button></div>
</form></div></div>
<style>
.school-user-form-page{max-width:900px;margin:0 auto;color:#1e293b}.school-user-form-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:28px}.school-user-form-card h2{margin:0 0 7px;font-size:23px}.school-user-form-card>p{color:#64748b;margin:0 0 24px}.school-user-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}.school-user-form-grid label{display:flex;flex-direction:column;gap:8px;font-size:13px;font-weight:700}.school-user-form-grid label span{color:#e11d48}.school-user-form-grid input,.school-user-form-grid select{min-height:44px;padding:10px 12px;border:1px solid #cbd5e1;border-radius:9px;font-size:14px;background:#fff}.school-user-form-grid input:focus,.school-user-form-grid select:focus{outline:2px solid #c7d2fe;border-color:#6366f1}.school-user-form-actions{display:flex;gap:12px;justify-content:flex-end;margin-top:24px;align-items:center}.school-user-form-actions a,.school-user-form-actions button{padding:12px 17px;border-radius:9px;font-size:13px;font-weight:700;text-decoration:none;cursor:pointer}.school-user-form-actions a{background:#f1f5f9;color:#334155}.school-user-form-actions button{background:#4f46e5;color:#fff;border:0}.school-user-help{font-size:12px;color:#64748b;margin-top:20px}.school-user-errors{background:#fff1f2;color:#9f1239;padding:13px;border-radius:9px;margin-bottom:20px}@media(max-width:640px){.school-user-form-card{padding:17px}.school-user-form-grid{grid-template-columns:1fr}.school-user-form-actions{flex-wrap:wrap}.school-user-form-actions a,.school-user-form-actions button{flex:1;text-align:center}}
</style>
@endsection

@extends('layouts.school')

@section('title', 'Compte enseignant')

@section('content')
<div style="max-width:760px;margin:auto;padding:20px">
    <h1>Compte de {{ $teacher->first_name }} {{ $teacher->last_name }}</h1>
    <p>Matricule : {{ $teacher->matricule }}</p>

    @if(session('success'))
        <p role="status" style="color:#166534">{{ session('success') }}</p>
    @endif
    @if($errors->any())
        <div role="alert" style="color:#b91c1c">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    @if($account)
        <p>Adresse : {{ $account->email }}</p>
        <p>Statut : {{ $account->is_active ? 'Actif' : 'Désactivé' }}</p>
        <form method="POST" action="{{ route('company.school.teachers.account.toggle', $teacher->id) }}">
            @csrf
            @method('PATCH')
            <button type="submit">{{ $account->is_active ? 'Désactiver' : 'Activer' }} le compte</button>
        </form>
    @elseif($teacher->user_id)
        <p role="alert">La fiche est liée à un compte introuvable ou non autorisé. Vérifiez ce lien avant toute création.</p>
    @else
        <form method="POST" action="{{ route('company.school.teachers.account.store', $teacher->id) }}">
            @csrf
            <div style="margin:12px 0">
                <label for="email">Email de connexion</label>
                <input id="email" type="email" name="email" required value="{{ old('email', $teacher->email) }}" style="display:block;width:100%;padding:10px">
            </div>
            <div style="margin:12px 0">
                <label for="password">Mot de passe (12 caractères minimum)</label>
                <input id="password" type="password" name="password" minlength="12" required autocomplete="new-password" style="display:block;width:100%;padding:10px">
            </div>
            <div style="margin:12px 0">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input id="password_confirmation" type="password" name="password_confirmation" minlength="12" required autocomplete="new-password" style="display:block;width:100%;padding:10px">
            </div>
            <button type="submit">Créer le compte enseignant</button>
        </form>
    @endif
</div>
@endsection

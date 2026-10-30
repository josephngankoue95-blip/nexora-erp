@extends('layouts.school')
@section('title', 'Configurer les permissions')
@section('page-title', 'Permissions de ' . $schoolUser->name)
@section('content')
<div class="school-card">
    <p><strong>{{ $schoolUser->name }}</strong> — {{ $schoolUser->email }} ({{ $schoolUser->role }})</p>
    @if($errors->any())<div class="school-errors">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('company.school.permissions.update', $schoolUser) }}">
        @csrf @method('PUT')
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead><tr><th style="text-align:left;padding:12px">Permission</th><th style="text-align:left;padding:12px">Décision</th></tr></thead>
                <tbody>
                @foreach($labels as $key => $label)
                    @php
                        $default = in_array('*', $defaults, true) || in_array($key, $defaults, true);
                        $choice = array_key_exists($key, $overrides) ? ($overrides[$key] ? 'allow' : 'deny') : 'default';
                    @endphp
                    <tr style="border-top:1px solid #e5eaf3">
                        <td style="padding:12px">{{ $label }}<br><small>{{ $key }}</small></td>
                        <td style="padding:12px">
                            <select name="permissions[{{ $key }}]" @disabled($key === 'school.permissions.manage')>
                                <option value="default" @selected(old('permissions.'.$key, $choice) === 'default')>Par défaut ({{ $default ? 'autorisé' : 'refusé' }})</option>
                                <option value="allow" @selected(old('permissions.'.$key, $choice) === 'allow')>Autoriser</option>
                                <option value="deny" @selected(old('permissions.'.$key, $choice) === 'deny')>Refuser</option>
                            </select>
                            @if($key === 'school.permissions.manage')
                                <input type="hidden" name="permissions[{{ $key }}]" value="default">
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <button type="submit" style="margin-top:16px;padding:12px 18px">Enregistrer</button>
        <a href="{{ route('company.school.permissions.index') }}" style="margin-left:12px">Retour</a>
    </form>
</div>
@endsection

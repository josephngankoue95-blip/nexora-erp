@extends('layouts.school')
@section('title', 'Permissions scolaires')
@section('page-title', 'Permissions scolaires')
@section('content')
<div class="school-card">
    <h2>Permissions par utilisateur</h2>
    <p>Les droits personnalisés complètent les droits par défaut du rôle. Seul l'administrateur principal peut les modifier.</p>
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse">
            <thead><tr><th style="text-align:left;padding:12px">Nom</th><th style="text-align:left;padding:12px">Email</th><th style="text-align:left;padding:12px">Rôle</th><th>Action</th></tr></thead>
            <tbody>
            @forelse($users as $schoolUser)
                <tr style="border-top:1px solid #e5eaf3">
                    <td style="padding:12px">{{ $schoolUser->name }}</td>
                    <td style="padding:12px">{{ $schoolUser->email }}</td>
                    <td style="padding:12px">{{ $schoolUser->role }}</td>
                    <td style="padding:12px"><a href="{{ route('company.school.permissions.edit', $schoolUser) }}">Configurer</a></td>
                </tr>
            @empty
                <tr><td colspan="4" style="padding:12px">Aucun utilisateur scolaire.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</div>
@endsection

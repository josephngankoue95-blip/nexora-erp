@extends('layouts.website')
@section('title', 'Pages du site')
@section('page-title', 'Pages du site')
@section('page-description', 'Créez, personnalisez et publiez les pages de votre site vitrine.')

@section('content')
<div class="wp-wrap">
    @if(session('success'))
        <div class="wp-alert" role="status">{{ session('success') }}</div>
    @endif

    <div class="wp-toolbar">
        <form method="GET" action="{{ route('company.website.pages.index') }}" class="wp-search">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher une page..." aria-label="Rechercher une page">
            <button type="submit">Rechercher</button>
        </form>
        <a class="wp-btn" href="{{ route('company.website.pages.create') }}">+ Nouvelle page</a>
    </div>

    <div class="wp-table-wrap">
        <table class="wp-table">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>Statut</th>
                    <th>Menu</th>
                    <th>Dernière modification</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                    <tr>
                        <td>
                            <strong>{{ $page->title }}</strong>
                            <small>/{{ $page->slug }}</small>
                        </td>
                        <td>
                            <span class="wp-pill {{ $page->status === 'published' ? 'is-live' : '' }}">
                                {{ $page->status === 'published' ? 'Publiée' : 'Brouillon' }}
                            </span>
                        </td>
                        <td>{{ $page->show_in_menu ? 'Oui' : 'Non' }}</td>
                        <td>{{ $page->updated_at?->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="wp-actions">
                                <a href="{{ route('company.website.pages.edit', $page->id) }}">Modifier</a>
                                <a href="{{ route('company.website.builder.edit', $page->id) }}" class="wp-btn" aria-label="Personnaliser visuellement la page {{ $page->title }}">🎨 Personnaliser</a>
                                <a href="{{ route('company.website.pages.preview', $page->id) }}" target="_blank" rel="noopener noreferrer">Aperçu</a>
                                <form method="POST" action="{{ route('company.website.pages.destroy', $page->id) }}" onsubmit="return confirm('Supprimer définitivement cette page ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="wp-empty">Aucune page pour le moment. Créez votre première page.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="wp-pagination">{{ $pages->links() }}</div>
</div>
@include('company.website.pages.styles')
@endsection

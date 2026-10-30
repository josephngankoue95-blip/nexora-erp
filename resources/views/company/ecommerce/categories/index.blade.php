
@extends('layouts.ecommerce')

@section('title', 'Catégories E-commerce')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ecommerce-categories.css') }}">
@endpush

@section('content')

<div class="ec-category-page">

    <div class="ec-category-header">
        <div>
            <h1>Catégories</h1>
            <p>Organisez les produits de votre boutique en catégories et sous-catégories.</p>
        </div>

        @if(auth()->user()->hasPermission('ecommerce.create'))
            <a href="{{ route('company.ecommerce.categories.create') }}"
               class="ec-btn ec-btn-primary">
                + Nouvelle catégorie
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="ec-alert ec-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="ec-alert ec-alert-error">
            {{ session('error') }}
        </div>
    @endif

    <div class="ec-stats">
        <div class="ec-stat">
            <span>Total catégories</span>
            <strong>{{ $totalCategories }}</strong>
        </div>

        <div class="ec-stat">
            <span>Catégories actives</span>
            <strong>{{ $activeCategories }}</strong>
        </div>

        <div class="ec-stat">
            <span>Catégories inactives</span>
            <strong>{{ $inactiveCategories }}</strong>
        </div>
    </div>

    <div class="ec-panel">

        <form method="GET"
              action="{{ route('company.ecommerce.categories.index') }}"
              class="ec-toolbar">

            <input type="search"
                   name="search"
                   value="{{ $search }}"
                   placeholder="Rechercher une catégorie..."
                   class="ec-input">

            <select name="status" class="ec-input">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>
                    Tous les statuts
                </option>
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>
                    Actives
                </option>
                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>
                    Inactives
                </option>
            </select>

            <button type="submit" class="ec-btn ec-btn-primary">
                Filtrer
            </button>

            <a href="{{ route('company.ecommerce.categories.index') }}"
               class="ec-btn ec-btn-secondary">
                Réinitialiser
            </a>
        </form>

        <div class="ec-table-scroll">
            <table class="ec-table">
                <thead>
                    <tr>
                        <th>Catégorie</th>
                        <th>Catégorie parente</th>
                        <th>Ordre</th>
                        <th>Sous-catégories</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <div class="ec-category-identity">
                                    @if($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}"
                                             alt="{{ $category->name }}"
                                             class="ec-category-thumb">
                                    @else
                                        <div class="ec-category-placeholder">📂</div>
                                    @endif

                                    <div>
                                        <strong>{{ $category->name }}</strong>
                                        <small>{{ $category->slug }}</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                {{ $category->parent?->name ?? 'Catégorie principale' }}
                            </td>

                            <td>{{ $category->sort_order }}</td>

                            <td>{{ $category->children_count }}</td>

                            <td>
                                <span class="ec-badge {{ $category->is_active ? 'ec-badge-active' : 'ec-badge-inactive' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            <td>
                                <div class="ec-actions">

                                    @if(auth()->user()->hasPermission('ecommerce.edit'))
                                        <a href="{{ route('company.ecommerce.categories.edit', $category->id) }}"
                                           class="ec-icon-btn"
                                           title="Modifier"
                                           aria-label="Modifier {{ $category->name }}">
                                            ✏️
                                        </a>

                                        <form method="POST"
                                              action="{{ route('company.ecommerce.categories.toggle-status', $category->id) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                    class="ec-icon-btn"
                                                    title="{{ $category->is_active ? 'Désactiver' : 'Activer' }}"
                                                    aria-label="{{ $category->is_active ? 'Désactiver' : 'Activer' }} {{ $category->name }}">
                                                {{ $category->is_active ? '⏸️' : '▶️' }}
                                            </button>
                                        </form>
                                    @endif

                                    @if(auth()->user()->hasPermission('ecommerce.delete'))
                                        <form method="POST"
                                              action="{{ route('company.ecommerce.categories.destroy', $category->id) }}"
                                              onsubmit="return confirm('Supprimer définitivement cette catégorie ?');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="ec-icon-btn ec-icon-danger"
                                                    title="Supprimer"
                                                    aria-label="Supprimer {{ $category->name }}">
                                                🗑️
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="ec-empty">
                                Aucune catégorie trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="ec-pagination">
                {{ $categories->links() }}
            </div>
        @endif

    </div>

</div>

@endsection

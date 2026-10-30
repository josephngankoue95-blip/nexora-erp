@extends('layouts.ecommerce')
@section('title', 'Collections E-commerce')
@section('page-title', 'Collections')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/ecommerce-collections.css') }}">
@endpush
@section('content')
<div class="ecol-page">
    <header class="ecol-header">
        <div><h1>Collections</h1><p>Organisez les sélections de produits de votre boutique.</p></div>
        @if(auth()->user()->hasPermission('ecommerce.create'))
            <a class="ecol-btn ecol-btn-primary" href="{{ route('company.ecommerce.collections.create') }}">+ Nouvelle collection</a>
        @endif
    </header>
    @if(session('success'))<div class="ecol-alert ecol-alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="ecol-alert ecol-alert-error">{{ session('error') }}</div>@endif
    <div class="ecol-stats">
        <div class="ecol-stat"><span>Total</span><strong>{{ $stats['total'] }}</strong></div>
        <div class="ecol-stat"><span>Actives</span><strong>{{ $stats['active'] }}</strong></div>
        <div class="ecol-stat"><span>Inactives</span><strong>{{ $stats['inactive'] }}</strong></div>
        <div class="ecol-stat"><span>À la une</span><strong>{{ $stats['featured'] }}</strong></div>
    </div>
    <section class="ecol-panel">
        <form class="ecol-filters" method="GET" action="{{ route('company.ecommerce.collections.index') }}">
            <input class="ecol-input" name="search" value="{{ $search }}" placeholder="Rechercher une collection...">
            <select class="ecol-input" name="status">
                <option value="">Tous les statuts</option>
                <option value="active" @selected($status === 'active')>Actives</option>
                <option value="inactive" @selected($status === 'inactive')>Inactives</option>
                <option value="featured" @selected($status === 'featured')>À la une</option>
            </select>
            <button class="ecol-btn ecol-btn-primary" type="submit">Filtrer</button>
            <a class="ecol-btn ecol-btn-outline" href="{{ route('company.ecommerce.collections.index') }}">Effacer</a>
        </form>
        <div class="ecol-table-wrap"><table class="ecol-table"><thead><tr>
            <th>Collection</th><th>Produits</th><th>Ordre</th><th>Statut</th><th>Mise en avant</th><th>Actions</th>
        </tr></thead><tbody>
        @forelse($collections as $collection)
            <tr>
                <td><div class="ecol-identity">
                    @if($collection->image)
                        <img class="ecol-thumb" src="{{ asset('storage/' . $collection->image) }}" alt="{{ $collection->name }}">
                    @else <div class="ecol-thumb ecol-thumb-empty">▦</div> @endif
                    <div><strong>{{ $collection->name }}</strong><small>{{ $collection->slug }}</small></div>
                </div></td>
                <td>{{ $collection->products_count }}</td>
                <td>{{ $collection->sort_order }}</td>
                <td><span class="ecol-pill {{ $collection->is_active ? 'ecol-pill-green' : 'ecol-pill-gray' }}">{{ $collection->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td>{{ $collection->is_featured ? 'Oui' : 'Non' }}</td>
                <td><div class="ecol-actions">
                    @if(auth()->user()->hasPermission('ecommerce.edit'))
                        <a class="ecol-btn ecol-btn-outline ecol-btn-small" href="{{ route('company.ecommerce.collections.edit', $collection->id) }}">Modifier</a>
                        <form method="POST" action="{{ route('company.ecommerce.collections.toggle-status', $collection->id) }}">@csrf @method('PATCH')
                            <button class="ecol-btn ecol-btn-outline ecol-btn-small" type="submit">{{ $collection->is_active ? 'Désactiver' : 'Activer' }}</button>
                        </form>
                    @endif
                    @if(auth()->user()->hasPermission('ecommerce.delete'))
                        <form method="POST" action="{{ route('company.ecommerce.collections.destroy', $collection->id) }}" onsubmit="return confirm('Supprimer définitivement cette collection ? Les produits resteront dans le catalogue.');">@csrf @method('DELETE')
                            <button class="ecol-btn ecol-btn-danger ecol-btn-small" type="submit">Supprimer</button>
                        </form>
                    @endif
                </div></td>
            </tr>
        @empty
            <tr><td colspan="6" class="ecol-empty">Aucune collection trouvée. Créez votre première collection pour commencer.</td></tr>
        @endforelse
        </tbody></table></div>
        <div class="ecol-pagination">{{ $collections->links() }}</div>
    </section>
</div>
@endsection

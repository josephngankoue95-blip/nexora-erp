@extends('layouts.company')

@section('title', 'Historique du stock')
@section('page-title', 'Historique du stock')

@section('content')

<div class="company-module">

    <div class="module-header">

        <div>

            <span class="module-kicker">
                STOCK
            </span>

            <h1>Historique</h1>

            <p>
                Consultez tous les mouvements de stock de votre entreprise.
            </p>

        </div>

        <a
            href="{{ route('company.stock.index') }}"
            class="module-btn module-btn-light"
        >
            ← Stock
        </a>

    </div>


    <div class="module-card">

        <form
            method="GET"
            action="{{ route('company.stock.history') }}"
            class="module-filters"
        >

            <div class="filter-group">

                <label>Recherche</label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Produit ou SKU..."
                >

            </div>


            <div class="filter-group">

                <label>Type</label>

                <select name="type">

                    <option value="">
                        Tous
                    </option>

                    <option
                        value="entry"
                        @selected(request('type') === 'entry')
                    >
                        Entrées
                    </option>

                    <option
                        value="exit"
                        @selected(request('type') === 'exit')
                    >
                        Sorties
                    </option>

                    <option
                        value="adjustment"
                        @selected(request('type') === 'adjustment')
                    >
                        Ajustements
                    </option>

                </select>

            </div>


            <div class="filter-actions">

                <button
                    type="submit"
                    class="module-btn module-btn-primary"
                >
                    Filtrer
                </button>

                <a
                    href="{{ route('company.stock.history') }}"
                    class="module-btn module-btn-light"
                >
                    Réinitialiser
                </a>

            </div>

        </form>

    </div>


    <div class="module-card">

        <div class="module-card-header">

            <h2>Mouvements</h2>

        </div>


        <div class="module-table-wrapper">

            <table class="module-table">

                <thead>

                    <tr>

                        <th>Date</th>

                        <th>Produit</th>

                        <th>Type</th>

                        <th>Quantité</th>

                        <th>Avant</th>

                        <th>Après</th>

                        <th>Motif</th>

                        <th>Utilisateur</th>

                    </tr>

                </thead>


                <tbody>

                @forelse($movements as $movement)

                    <tr>

                        <td>
                            {{ $movement->created_at->format('d/m/Y H:i') }}
                        </td>


                        <td>

                            <strong>
                                {{ $movement->product->name }}
                            </strong>

                            <small>
                                {{ $movement->product->sku }}
                            </small>

                        </td>


                        <td>

                            @if($movement->type === 'entry')

                                <span class="stock-badge stock-success">
                                    Entrée
                                </span>

                            @elseif($movement->type === 'exit')

                                <span class="stock-badge stock-danger">
                                    Sortie
                                </span>

                            @else

                                <span class="stock-badge stock-warning">
                                    Ajustement
                                </span>

                            @endif

                        </td>


                        <td>

                            <strong>
                                {{ $movement->quantity }}
                            </strong>

                        </td>


                        <td>
                            {{ $movement->stock_before }}
                        </td>


                        <td>
                            <strong>
                                {{ $movement->stock_after }}
                            </strong>
                        </td>


                        <td>
                            {{ $movement->reason ?? '—' }}
                        </td>


                        <td>
                            {{ $movement->user?->name ?? 'Système' }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="empty-state"
                        >

                            <strong>
                                Aucun mouvement
                            </strong>

                            <p>
                                L'historique apparaîtra ici après les premières entrées ou sorties.
                            </p>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($movements->hasPages())

            <div class="module-pagination">

                {{ $movements->links() }}

            </div>

        @endif

    </div>

</div>

@endsection
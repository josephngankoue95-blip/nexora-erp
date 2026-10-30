@extends('layouts.company')

@section('title', 'Sortie de stock')
@section('page-title', 'Sortie de stock')

@section('content')

<div class="company-module">

    <div class="module-header">

        <div>

            <span class="module-kicker">
                STOCK
            </span>

            <h1>Sortie de stock</h1>

            <p>
                Retirez une quantité du stock d'un produit.
            </p>

        </div>

        <a
            href="{{ route('company.stock.index') }}"
            class="module-btn module-btn-light"
        >
            ← Retour
        </a>

    </div>


    @if($errors->any())

        <div class="module-alert module-alert-danger">

            <ul>

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('company.stock.exit.store') }}"
    >

        @csrf

        <div class="module-card">

            <div class="module-card-header">

                <h2>Nouvelle sortie</h2>

                <span>
                    Retrait de marchandises
                </span>

            </div>


            <div class="form-grid">

                <div class="form-group form-group-full">

                    <label>
                        Produit *
                    </label>

                    <select
                        name="product_id"
                        required
                    >

                        <option value="">
                            Sélectionner un produit
                        </option>

                        @foreach($products as $product)

                            <option
                                value="{{ $product->id }}"
                                @selected(old('product_id') == $product->id)
                            >

                                {{ $product->name }}
                                — {{ $product->sku }}
                                — Disponible : {{ $product->stock_quantity }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Quantité *
                    </label>

                    <input
                        type="number"
                        name="quantity"
                        value="{{ old('quantity') }}"
                        min="1"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Motif *
                    </label>

                    <select
                        name="reason"
                        required
                    >

                        <option value="">
                            Sélectionner
                        </option>

                        <option value="Vente">
                            Vente
                        </option>

                        <option value="Casse">
                            Casse
                        </option>

                        <option value="Perte">
                            Perte
                        </option>

                        <option value="Retour fournisseur">
                            Retour fournisseur
                        </option>

                        <option value="Correction">
                            Correction
                        </option>

                        <option value="Autre">
                            Autre
                        </option>

                    </select>

                </div>


                <div class="form-group form-group-full">

                    <label>
                        Note
                    </label>

                    <textarea
                        name="note"
                        rows="4"
                        placeholder="Informations complémentaires..."
                    >{{ old('note') }}</textarea>

                </div>

            </div>

        </div>


        <div class="form-actions">

            <a
                href="{{ route('company.stock.index') }}"
                class="module-btn module-btn-light"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="module-btn module-btn-primary"
            >
                Enregistrer la sortie
            </button>

        </div>

    </form>

</div>

@endsection
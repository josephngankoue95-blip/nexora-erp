@extends('layouts.company')

@section('title', 'Entrée de stock')
@section('page-title', 'Entrée de stock')

@section('content')

<div class="company-module">

    <div class="module-header">

        <div>

            <span class="module-kicker">
                STOCK
            </span>

            <h1>Entrée de stock</h1>

            <p>
                Ajoutez des quantités au stock d'un produit.
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
        action="{{ route('company.stock.entry.store') }}"
    >

        @csrf

        <div class="module-card">

            <div class="module-card-header">

                <h2>Nouvelle entrée</h2>

                <span>
                    Enregistrement d'un approvisionnement
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
                                — Stock : {{ $product->stock_quantity }}

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

                        <option value="Achat">
                            Achat
                        </option>

                        <option value="Approvisionnement">
                            Approvisionnement
                        </option>

                        <option value="Retour client">
                            Retour client
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
                Enregistrer l'entrée
            </button>

        </div>

    </form>

</div>

@endsection
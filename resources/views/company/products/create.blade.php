@extends('layouts.company')

@section('title', 'Nouveau produit')
@section('page-title', 'Nouveau produit')

@section('content')

<div class="company-module">

    <div class="module-header">

        <div>
            <span class="module-kicker">
                PRODUITS
            </span>

            <h1>Nouveau produit</h1>

            <p>
                Ajoutez un nouveau produit à votre catalogue.
            </p>
        </div>

        <a
            href="{{ route('company.products.index') }}"
            class="module-btn module-btn-light"
        >
            ← Retour
        </a>

    </div>


    @if($errors->any())

        <div class="module-alert module-alert-danger">

            <strong>
                Vérifiez les informations saisies.
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('company.products.store') }}"
    >

        @csrf

        <div class="module-card">

            <div class="module-card-header">

                <div>
                    <h2>Informations générales</h2>
                    <span>Identité du produit</span>
                </div>

            </div>


            <div class="form-grid">

                <div class="form-group form-group-full">

                    <label>
                        Nom du produit *
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Ex : Ordinateur HP ProBook"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        SKU / Référence *
                    </label>

                    <input
                        type="text"
                        name="sku"
                        value="{{ old('sku') }}"
                        placeholder="Ex : HP-PB-001"
                        required
                    >

                    <small>
                        Référence unique dans votre entreprise.
                    </small>

                </div>


                <div class="form-group">

                    <label>
                        Code-barres
                    </label>

                    <input
                        type="text"
                        name="barcode"
                        value="{{ old('barcode') }}"
                        placeholder="Ex : 890123456789"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Catégorie
                    </label>

                    <select name="category_id">

                        <option value="">
                            Sans catégorie
                        </option>

                        @foreach($categories as $category)

                            <option
                                value="{{ $category->id }}"
                                @selected(old('category_id') == $category->id)
                            >
                                {{ $category->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="form-group form-group-full">

                    <label>
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        placeholder="Description du produit..."
                    >{{ old('description') }}</textarea>

                </div>

            </div>

        </div>


        <div class="module-card">

            <div class="module-card-header">

                <div>
                    <h2>Prix et stock</h2>
                    <span>Informations commerciales</span>
                </div>

            </div>


            <div class="form-grid">

                <div class="form-group">

                    <label>
                        Prix d'achat *
                    </label>

                    <div class="input-with-suffix">

                        <input
                            type="number"
                            name="purchase_price"
                            value="{{ old('purchase_price', 0) }}"
                            min="0"
                            step="0.01"
                            required
                        >

                        <span>FCFA</span>

                    </div>

                </div>


                <div class="form-group">

                    <label>
                        Prix de vente *
                    </label>

                    <div class="input-with-suffix">

                        <input
                            type="number"
                            name="selling_price"
                            value="{{ old('selling_price', 0) }}"
                            min="0"
                            step="0.01"
                            required
                        >

                        <span>FCFA</span>

                    </div>

                </div>


                <div class="form-group">

                    <label>
                        Stock initial *
                    </label>

                    <input
                        type="number"
                        name="stock_quantity"
                        value="{{ old('stock_quantity', 0) }}"
                        min="0"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Seuil d'alerte *
                    </label>

                    <input
                        type="number"
                        name="min_stock"
                        value="{{ old('min_stock', 0) }}"
                        min="0"
                        required
                    >

                    <small>
                        Une alerte sera affichée lorsque le stock atteindra ce seuil.
                    </small>

                </div>


                <div class="form-group">

                    <label>
                        Statut
                    </label>

                    <select name="is_active">

                        <option
                            value="1"
                            @selected(old('is_active', '1') == '1')
                        >
                            Actif
                        </option>

                        <option
                            value="0"
                            @selected(old('is_active') === '0')
                        >
                            Inactif
                        </option>

                    </select>

                </div>

            </div>

        </div>


        <div class="form-actions">

            <a
                href="{{ route('company.products.index') }}"
                class="module-btn module-btn-light"
            >
                Annuler
            </a>

            <button
                type="submit"
                class="module-btn module-btn-primary"
            >
                Enregistrer le produit
            </button>

        </div>

    </form>

</div>

@endsection
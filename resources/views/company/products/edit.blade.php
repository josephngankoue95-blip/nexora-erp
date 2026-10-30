@extends('layouts.company')

@section('title', 'Modifier le produit')
@section('page-title', 'Modifier le produit')

@section('content')

<div class="company-module">

    <div class="module-header">

        <div>
            <span class="module-kicker">
                PRODUITS
            </span>

            <h1>Modifier le produit</h1>

            <p>
                {{ $product->name }}
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

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('company.products.update', $product->id) }}"
    >

        @csrf
        @method('PUT')


        <div class="module-card">

            <div class="module-card-header">
                <div>
                    <h2>Informations générales</h2>
                    <span>Identité du produit</span>
                </div>
            </div>


            <div class="form-grid">

                <div class="form-group form-group-full">

                    <label>Nom du produit *</label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $product->name) }}"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>SKU / Référence *</label>

                    <input
                        type="text"
                        name="sku"
                        value="{{ old('sku', $product->sku) }}"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Code-barres</label>

                    <input
                        type="text"
                        name="barcode"
                        value="{{ old('barcode', $product->barcode) }}"
                    >

                </div>


                <div class="form-group">

                    <label>Catégorie</label>

                    <select name="category_id">

                        <option value="">
                            Sans catégorie
                        </option>

                        @foreach($categories as $category)

                            <option
                                value="{{ $category->id }}"
                                @selected(old('category_id', $product->category_id) == $category->id)
                            >
                                {{ $category->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="form-group form-group-full">

                    <label>Description</label>

                    <textarea
                        name="description"
                        rows="4"
                    >{{ old('description', $product->description) }}</textarea>

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

                    <label>Prix d'achat *</label>

                    <input
                        type="number"
                        name="purchase_price"
                        value="{{ old('purchase_price', $product->purchase_price) }}"
                        min="0"
                        step="0.01"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Prix de vente *</label>

                    <input
                        type="number"
                        name="selling_price"
                        value="{{ old('selling_price', $product->selling_price) }}"
                        min="0"
                        step="0.01"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Stock *</label>

                    <input
                        type="number"
                        name="stock_quantity"
                        value="{{ old('stock_quantity', $product->stock_quantity) }}"
                        min="0"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Seuil d'alerte *</label>

                    <input
                        type="number"
                        name="min_stock"
                        value="{{ old('min_stock', $product->min_stock) }}"
                        min="0"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>Statut</label>

                    <select name="is_active">

                        <option
                            value="1"
                            @selected(old('is_active', $product->is_active ? '1' : '0') == '1')
                        >
                            Actif
                        </option>

                        <option
                            value="0"
                            @selected(old('is_active', $product->is_active ? '1' : '0') == '0')
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
                Enregistrer les modifications
            </button>

        </div>

    </form>

</div>

@endsection
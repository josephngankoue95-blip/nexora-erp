@extends('layouts.company')

@section('title', 'Détails de la catégorie')

@section('content')

<div class="page-header">

    <div>

        <h1>
            {{ $category->name }}
        </h1>

        <p>
            Détails de la catégorie.
        </p>

    </div>


    <div>

        <a
            href="{{ route('company.categories.edit', $category) }}"
            class="btn btn-primary"
        >
            Modifier
        </a>

        <a
            href="{{ route('company.categories.index') }}"
            class="btn btn-secondary"
        >
            ← Retour
        </a>

    </div>

</div>


<div class="dashboard-section">

    <div class="dashboard-section-header">

        <div>

            <h2>
                Informations
            </h2>

        </div>

    </div>


    <div class="details-grid">

        <div class="detail-item">

            <span class="detail-label">
                Nom
            </span>

            <strong>
                {{ $category->name }}
            </strong>

        </div>


        <div class="detail-item">

            <span class="detail-label">
                Slug
            </span>

            <strong>
                {{ $category->slug }}
            </strong>

        </div>


        <div class="detail-item">

            <span class="detail-label">
                Statut
            </span>

            @if($category->is_active)

                <span class="badge badge-success">
                    Active
                </span>

            @else

                <span class="badge badge-danger">
                    Inactive
                </span>

            @endif

        </div>


        <div class="detail-item">

            <span class="detail-label">
                Créée le
            </span>

            <strong>
                {{ $category->created_at->format('d/m/Y H:i') }}
            </strong>

        </div>


        <div class="detail-item detail-full">

            <span class="detail-label">
                Description
            </span>

            <p>
                {{ $category->description ?? 'Aucune description.' }}
            </p>

        </div>

    </div>

</div>

@endsection
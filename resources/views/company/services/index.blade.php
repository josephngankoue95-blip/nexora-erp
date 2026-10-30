@extends('layouts.company')

@section('title', 'Prestations')

@section('content')

<div class="services-page">

    <div class="services-header">

        <div>
            <div class="services-eyebrow">
                CATALOGUE
            </div>

            <h1>Prestations</h1>

            <p>
                Gérez les services proposés par votre entreprise.
            </p>
        </div>

        @canPermission('services.create')
            <a
                href="{{ route('company.services.create') }}"
                class="services-primary-button"
            >
                <span>+</span>
                Nouvelle prestation
            </a>
        @endcanPermission

    </div>


    {{-- Message de succès --}}
    @if(session('success'))

        <div class="services-alert services-alert-success">
            <span class="services-alert-icon">✓</span>

            <span>
                {{ session('success') }}
            </span>
        </div>

    @endif


    {{-- Statistiques --}}
    <div class="services-stats">

        <div class="service-stat-card">

            <div class="service-stat-icon">
                ☷
            </div>

            <div>
                <span>Total prestations</span>

                <strong>
                    {{ number_format($statistics['total'], 0, ',', ' ') }}
                </strong>
            </div>

        </div>


        <div class="service-stat-card">

            <div class="service-stat-icon service-stat-icon-success">
                ✓
            </div>

            <div>
                <span>Prestations actives</span>

                <strong>
                    {{ number_format($statistics['active'], 0, ',', ' ') }}
                </strong>
            </div>

        </div>


        <div class="service-stat-card">

            <div class="service-stat-icon service-stat-icon-warning">
                ◷
            </div>

            <div>
                <span>Prestations inactives</span>

                <strong>
                    {{ number_format($statistics['inactive'], 0, ',', ' ') }}
                </strong>
            </div>

        </div>


        <div class="service-stat-card">

            <div class="service-stat-icon service-stat-icon-info">
                #
            </div>

            <div>
                <span>Catégories</span>

                <strong>
                    {{ number_format($statistics['categories'], 0, ',', ' ') }}
                </strong>
            </div>

        </div>

    </div>


    {{-- Filtres --}}
    <div class="services-filter-card">

        <form
            method="GET"
            action="{{ route('company.services.index') }}"
            class="services-filter-form"
        >

            <div class="services-search">

                <span class="services-search-icon">
                    ⌕
                </span>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Rechercher une prestation..."
                >

            </div>


            <select name="category">

                <option value="">
                    Toutes les catégories
                </option>

                @foreach($categories as $category)

                    <option
                        value="{{ $category }}"
                        @selected(request('category') === $category)
                    >
                        {{ $category }}
                    </option>

                @endforeach

            </select>


            <select name="status">

                <option value="">
                    Tous les statuts
                </option>

                <option
                    value="active"
                    @selected(request('status') === 'active')
                >
                    Actives
                </option>

                <option
                    value="inactive"
                    @selected(request('status') === 'inactive')
                >
                    Inactives
                </option>

            </select>


            <button
                type="submit"
                class="services-filter-button"
            >
                Filtrer
            </button>


            @if(request()->hasAny([
                'search',
                'category',
                'status'
            ]))

                <a
                    href="{{ route('company.services.index') }}"
                    class="services-reset-button"
                >
                    Réinitialiser
                </a>

            @endif

        </form>

    </div>


    {{-- Tableau --}}
    <div class="services-table-card">

        <div class="services-table-header">

            <div>
                <h2>Liste des prestations</h2>

                <p>
                    {{ $services->total() }}
                    prestation(s)
                </p>
            </div>

        </div>


        @if($services->count())

            <div class="services-table-wrapper">

                <table class="services-table">

                    <thead>

                        <tr>
                            <th>Prestation</th>
                            <th>Référence</th>
                            <th>Catégorie</th>
                            <th>Prix</th>
                            <th>Taxe</th>
                            <th>Statut</th>
                            <th class="services-actions-column">
                                Actions
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach($services as $service)

                            <tr>

                                <td>

                                    <div class="service-name">

                                        <div class="service-avatar">
                                            🔧
                                        </div>

                                        <div>

                                            <strong>
                                                {{ $service->name }}
                                            </strong>

                                            @if($service->description)

                                                <small>
                                                    {{ \Illuminate\Support\Str::limit(
                                                        $service->description,
                                                        70
                                                    ) }}
                                                </small>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <span class="service-reference">
                                        {{ $service->reference ?: '—' }}
                                    </span>

                                </td>


                                <td>

                                    @if($service->category)

                                        <span class="service-category">
                                            {{ $service->category }}
                                        </span>

                                    @else

                                        <span class="service-muted">
                                            Non classée
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    <strong class="service-price">
                                        {{ number_format(
                                            (float) $service->price,
                                            0,
                                            ',',
                                            ' '
                                        ) }}
                                        FCFA
                                    </strong>

                                </td>


                                <td>

                                    @if((float) $service->tax > 0)

                                        {{ number_format(
                                            (float) $service->tax,
                                            2,
                                            ',',
                                            ' '
                                        ) }} %

                                    @else

                                        <span class="service-muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    @if($service->is_active)

                                        <span class="service-status service-status-active">
                                            <i></i>
                                            Actif
                                        </span>

                                    @else

                                        <span class="service-status service-status-inactive">
                                            <i></i>
                                            Inactif
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    <div class="service-actions">

                                        @canPermission('services.edit')

                                            <a
                                                href="{{ route(
                                                    'company.services.edit',
                                                    $service
                                                ) }}"
                                                class="service-action service-action-edit"
                                                title="Modifier"
                                            >
                                                ✎
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'company.services.toggle-status',
                                                    $service
                                                ) }}"
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="service-action service-action-status"
                                                    title="{{ $service->is_active ? 'Désactiver' : 'Activer' }}"
                                                >
                                                    {{ $service->is_active ? '◉' : '○' }}
                                                </button>

                                            </form>

                                        @endcanPermission


                                        @canPermission('services.delete')

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'company.services.destroy',
                                                    $service
                                                ) }}"
                                                onsubmit="return confirm(
                                                    'Voulez-vous vraiment supprimer cette prestation ?'
                                                );"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="service-action service-action-delete"
                                                    title="Supprimer"
                                                >
                                                    ×
                                                </button>

                                            </form>

                                        @endcanPermission

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            <div class="services-pagination">
                {{ $services->links() }}
            </div>

        @else

            <div class="services-empty">

                <div class="services-empty-icon">
                    🔧
                </div>

                <h3>
                    Aucune prestation trouvée
                </h3>

                <p>
                    Commencez par créer votre première prestation
                    de service.
                </p>

                @canPermission('services.create')

                    <a
                        href="{{ route('company.services.create') }}"
                        class="services-primary-button"
                    >
                        <span>+</span>
                        Ajouter une prestation
                    </a>

                @endcanPermission

            </div>

        @endif

    </div>

</div>

@endsection
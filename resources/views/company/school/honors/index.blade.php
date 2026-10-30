
@extends('layouts.school')

@section('title', "Tableaux d'honneur")
@section('page-title', "Tableaux d'honneur")
@section('page-description', 'Distinctions proposées et décisions officielles des conseils de classe')

@section('content')

@vite('resources/css/school-honors.css')

<div class="honors-wrap">

    {{-- EN-TÊTE --}}
    <div class="honors-head">
        <h2>Tableaux d'honneur</h2>

        @if(\Illuminate\Support\Facades\Route::has('company.school.honors.settings'))
            <a
                class="honors-link"
                href="{{ route('company.school.honors.settings') }}">
                Configurer les seuils
            </a>
        @endif
    </div>

    {{-- FILTRES --}}
    <form
        method="GET"
        action="{{ route('company.school.honors.index') }}"
        class="honors-filters">

        <label>
            Année scolaire

            <select name="year_id" required>
                <option value="">Choisir</option>

                @foreach($years as $item)
                    <option
                        value="{{ $item->id }}"
                        @selected($year?->id == $item->id)>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>
            Classe

            <select name="class_id" required>
                <option value="">Choisir</option>

                @foreach($classes as $item)
                    <option
                        value="{{ $item->id }}"
                        @selected($class?->id == $item->id)>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>
            Période

            <select name="kind">
                <option
                    value="trimester"
                    @selected($kind === 'trimester')>
                    Trimestre
                </option>

                @if(!$config || $config->mode === 'sequences')
                    <option
                        value="sequence"
                        @selected($kind === 'sequence')>
                        Séquence
                    </option>
                @endif

                <option
                    value="annual"
                    @selected($kind === 'annual')>
                    Année entière
                </option>
            </select>
        </label>

        <label>
            Numéro

            <select name="number">
                @for($i = 1; $i <= 6; $i++)
                    <option
                        value="{{ $i }}"
                        @selected((int) $number === $i)>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </label>

        <button type="submit">
            Afficher
        </button>
    </form>

    {{-- CONFIGURATION MANQUANTE --}}
    @if($class && !$config)
        <p class="honors-alert">
            Configurez d'abord les périodes de notation
            pour cette année scolaire.
        </p>
    @endif

    {{-- RÉSULTATS --}}
    @if($class && $config)

        <div class="honors-actions">

            <p>
                <strong>{{ $class->name }}</strong>
                —
                {{ $year->name }}

                ·

                {{ $students->filter(fn ($s) => $s->distinction !== null)->count() }}
                distinction(s)
            </p>

            {{-- IMPRESSION OFFICIELLE --}}
            @if($councilValidated)

                <a
                    class="honors-link"
                    target="_blank"
                    rel="noopener"
                    href="{{ route('company.school.honors.print', [
                        'year_id' => $year->id,
                        'class_id' => $class->id,
                        'kind' => $kind,
                        'number' => $number,
                    ]) }}">

                    🖨 Imprimer les tableaux d'honneur officiels
                </a>

            @else

                <span class="honors-alert" role="status">
                    Impression officielle disponible après
                    validation du conseil de classe.
                </span>

                @if(\Illuminate\Support\Facades\Route::has('company.school.councils.index'))

                    <a
                        class="honors-link"
                        href="{{ route('company.school.councils.index', [
                            'year_id' => $year->id,
                            'class_id' => $class->id,
                            'kind' => $kind,
                            'number' => $number,
                        ]) }}">

                        Ouvrir les conseils de classe
                    </a>

                @endif

            @endif

        </div>

        {{-- TABLEAU DES ÉLÈVES --}}
        <div class="honors-table-scroll">

            <table class="honors-table">

                <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Matricule</th>
                        <th>Nom et prénom</th>
                        <th>Moyenne /20</th>
                        <th>Distinction proposée</th>
                        <th>Décision officielle</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($students as $student)

                        <tr>

                            <td>
                                {{ $student->rank ?? '—' }}
                            </td>

                            <td>
                                {{ $student->student_number }}
                            </td>

                            <td>
                                {{ $student->last_name }}
                                {{ $student->first_name }}
                            </td>

                            <td>
                                {{ $student->overall === null
                                    ? '—'
                                    : number_format(
                                        $student->overall,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                            </td>

                            <td>
                                {{ $student->proposed_distinction ?? '—' }}
                            </td>

                            <td>
                                {{ $student->official_decision ?? '—' }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6">
                                Aucun élève inscrit.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- NOTE SUR LA VALIDATION --}}
        <p class="honors-note">

            {{ $councilValidated
                ? 'Conseil validé : seules les décisions approuvées sont officielles.'
                : 'Conseil non validé : les distinctions affichées sont uniquement des propositions. Aucun certificat officiel ne peut être délivré.' }}

        </p>

    @endif

</div>

@endsection

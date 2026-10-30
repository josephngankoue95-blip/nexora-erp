
@extends('company.school.principal.layouts.app')

@section('title', 'Bulletins scolaires')

@section('content')

<section class="welcome">
    <div>
        <span class="eyebrow">DOCUMENTS SCOLAIRES</span>
        <h1>Bulletins scolaires</h1>
        <p>
            Consultation et impression des bulletins
            séquentiels, trimestriels et annuels.
        </p>
    </div>

    <div class="year">
        {{ $year->name ?? 'Aucune année active' }}
    </div>
</section>

<section class="panel">

    <h2>Sélectionner une classe</h2>

    @if($year)
        <form method="GET"
              action="{{ route('company.school.principal.bulletins.index') }}"
              class="teacher-filters">

            <select name="class_id" required>
                <option value="">Choisir une classe</option>

                @foreach($classes as $classe)
                    <option value="{{ $classe->id }}"
                        @selected($selectedClass?->id == $classe->id)>
                        {{ $classe->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit">
                Afficher les élèves
            </button>
        </form>
    @else
        <p>Aucune année scolaire active.</p>
    @endif

</section>

@if($selectedClass)

<section class="panel">

    <h2>Bulletins — {{ $selectedClass->name }}</h2>

    @if(!$config)
        <p>
            La configuration des évaluations
            n'est pas encore disponible pour cette année.
        </p>
    @else

        <form id="bulletinFilters" class="teacher-filters">
            <select id="bulletinKind">
                @if($config->mode === 'sequences')
                    <option value="sequence">Séquence</option>
                @endif

                <option value="trimester">Trimestre</option>
                <option value="annual">Annuel</option>
            </select>

            <select id="bulletinNumber">
                @for($i = 1; $i <= ($config->mode === 'sequences' ? 6 : 3); $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </form>

        <div class="academic-table-wrap">
            <table class="academic-table">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Élève</th>
                        <th>Consultation</th>
                        <th>PDF</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($students as $student)
                        @php
                            $bulletinUrl = route(
                                'company.school.principal.bulletins.show',
                                [
                                    'classId' => $selectedClass->id,
                                    'studentId' => $student->id,
                                ]
                            );
                        @endphp

                        <tr>
                            <td>{{ $student->student_number }}</td>

                            <td>
                                {{ $student->last_name }}
                                {{ $student->first_name }}
                            </td>

                            <td>
                                <a href="{{ $bulletinUrl }}"
                                   data-bulletin-url="{{ $bulletinUrl }}"
                                   class="teacher-detail-link bulletin-action"
                                   target="_blank"
                                   rel="noopener">
                                    Consulter
                                </a>
                            </td>

                            <td>
                                <a href="{{ $bulletinUrl }}"
                                   data-bulletin-url="{{ $bulletinUrl }}"
                                   data-pdf="1"
                                   class="teacher-detail-link bulletin-action">
                                    Télécharger PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                Aucun élève inscrit dans cette classe.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

</section>

@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const kind = document.getElementById('bulletinKind');
    const number = document.getElementById('bulletinNumber');

    if (!kind || !number) return;

    function updateNumbers() {
        const previous = number.value;
        const limit = kind.value === 'sequence' ? 6
                    : kind.value === 'trimester' ? 3
                    : 1;

        number.innerHTML = '';

        for (let i = 1; i <= limit; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            number.appendChild(option);
        }

        number.value = Number(previous) <= limit ? previous : '1';
        number.disabled = kind.value === 'annual';
    }

    function updateLinks() {
        document.querySelectorAll('.bulletin-action').forEach(link => {
            const url = new URL(link.dataset.bulletinUrl);
            url.searchParams.set('kind', kind.value);
            url.searchParams.set('number', number.value);

            if (link.dataset.pdf === '1') {
                url.searchParams.set('pdf', '1');
            }

            link.href = url.toString();
        });
    }

    kind.addEventListener('change', () => {
        updateNumbers();
        updateLinks();
    });

    number.addEventListener('change', updateLinks);

    updateNumbers();
    updateLinks();
});
</script>
@endpush

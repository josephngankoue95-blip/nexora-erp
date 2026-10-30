
@extends('company.school.principal.layouts.app')

@section('title', 'Supervision de la scolarité')

@section('content')

<section class="welcome">
    <div>
        <span class="eyebrow">GESTION FINANCIÈRE SCOLAIRE</span>
        <h1>Supervision de la scolarité</h1>
        <p>
            Suivi des frais scolaires, des paiements
            et du recouvrement par classe.
        </p>
    </div>

    <div class="year">
        {{ $year->name ?? 'Aucune année active' }}
    </div>
</section>

@if(!$year)
    <section class="panel">
        <p>Aucune année scolaire active.</p>
    </section>
@else

<section class="stats">
    <article class="stat">
        <span>Montant attendu</span>
        <strong>
            {{ number_format($stats['expected'], 0, ',', ' ') }}
        </strong>
        <small>FCFA</small>
    </article>

    <article class="stat">
        <span>Montant encaissé</span>
        <strong>
            {{ number_format($stats['collected'], 0, ',', ' ') }}
        </strong>
        <small>FCFA</small>
    </article>

    <article class="stat">
        <span>Reste à recouvrer</span>
        <strong>
            {{ number_format($stats['remaining'], 0, ',', ' ') }}
        </strong>
        <small>FCFA</small>
    </article>

    <article class="stat">
        <span>Taux de recouvrement brut</span>
        <strong>{{ $stats['rate'] }} %</strong>
        <small>Encaissements / attendu</small>
    </article>
</section>

<section class="panel">
    <h2>Situation des élèves</h2>

    <div class="stats">
        <article class="stat">
            <span>Dossiers de frais</span>
            <strong>{{ $stats['students'] }}</strong>
        </article>

        <article class="stat">
            <span>Soldés</span>
            <strong>{{ $stats['paid'] }}</strong>
        </article>

        <article class="stat">
            <span>Paiement partiel</span>
            <strong>{{ $stats['partial'] }}</strong>
        </article>

        <article class="stat">
            <span>Aucun paiement</span>
            <strong>{{ $stats['unpaid'] }}</strong>
        </article>
    </div>
</section>

<section class="panel">
    <h2>Recouvrement par classe</h2>

    <div class="academic-table-wrap">
        <table class="academic-table">
            <thead>
                <tr>
                    <th>Classe</th>
                    <th>Élèves</th>
                    <th>Attendu</th>
                    <th>Encaissé</th>
                    <th>Reste</th>
                    <th>Taux</th>
                </tr>
            </thead>

            <tbody>
                @forelse($byClass as $item)
                    <tr>
                        <td>{{ $item->class_name }}</td>
                        <td>{{ $item->students }}</td>

                        <td>
                            {{ number_format($item->expected, 0, ',', ' ') }}
                        </td>

                        <td>
                            {{ number_format($item->collected, 0, ',', ' ') }}
                        </td>

                        <td>
                            {{ number_format($item->remaining, 0, ',', ' ') }}
                        </td>

                        <td>
                            {{ number_format($item->rate, 2, ',', ' ') }} %
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            Aucun dossier de scolarité disponible.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <h2>Encaissements par mode de paiement</h2>

    <div class="academic-table-wrap">
        <table class="academic-table">
            <thead>
                <tr>
                    <th>Mode</th>
                    <th>Nombre de paiements</th>
                    <th>Montant</th>
                </tr>
            </thead>

            <tbody>
                @forelse($byMethod as $item)
                    <tr>
                        <td>{{ $item->method ?: 'Non renseigné' }}</td>
                        <td>{{ $item->payment_count }}</td>
                        <td>
                            {{ number_format($item->total, 0, ',', ' ') }}
                            FCFA
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            Aucun paiement enregistré.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <h2>Suivi individuel des élèves</h2>

    <div class="academic-table-wrap">
        <table class="academic-table">
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Élève</th>
                    <th>Classe</th>
                    <th>Attendu</th>
                    <th>Payé</th>
                    <th>Reste</th>
                    <th>Situation</th>
                </tr>
            </thead>

            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>{{ $student->student_number }}</td>

                        <td>
                            {{ $student->last_name }}
                            {{ $student->first_name }}
                        </td>

                        <td>{{ $student->class_name }}</td>

                        <td>
                            {{ number_format($student->expected, 0, ',', ' ') }}
                        </td>

                        <td>
                            {{ number_format($student->paid_amount, 0, ',', ' ') }}
                        </td>

                        <td>
                            {{ number_format($student->remaining, 0, ',', ' ') }}
                        </td>

                        <td>
                            @if($student->status === 'paid')
                                <span class="teacher-status is-active">
                                    Soldé
                                </span>
                            @elseif($student->status === 'partial')
                                <span class="teacher-status is-inactive">
                                    Partiel
                                </span>
                            @else
                                <span class="teacher-status is-inactive">
                                    Impayé
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            Aucun élève avec un dossier de frais.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@endif

@endsection

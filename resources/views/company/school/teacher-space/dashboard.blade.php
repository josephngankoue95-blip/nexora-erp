
@extends('layouts.school')

@section('title', 'Espace enseignant')

@section('page-title', 'Mon espace enseignant')

@section(
    'page-description',
    'Consultez vos classes, matières et affectations.'
)

@section('content')

<div class="teacher-dashboard">

    <div class="teacher-welcome">
        <h2>
            Bienvenue,
            {{ $teacher->first_name }}
            {{ $teacher->last_name }}
        </h2>

        <p>
            Retrouvez vos informations pédagogiques.
        </p>
    </div>

    <div class="teacher-stats">

        <div class="teacher-stat">
            <span>Classes affectées</span>
            <strong>{{ $classes->count() }}</strong>
        </div>

        <div class="teacher-stat">
            <span>Matières enseignées</span>
            <strong>{{ $subjects->count() }}</strong>
        </div>

        <div class="teacher-stat">
            <span>Affectations actives</span>
            <strong>{{ $assignments->count() }}</strong>
        </div>

    </div>

    <div class="school-card">
        <h3>Mes affectations</h3>

        @forelse($assignments as $assignment)

            @php
                $class = $classes->firstWhere(
                    'id',
                    $assignment->class_id
                );

                $subject = $subjects->firstWhere(
                    'id',
                    $assignment->subject_id
                );
            @endphp

            <div class="teacher-assignment">
                <div>
                    <strong>
                        {{ $subject?->name ?? 'Matière' }}
                    </strong>

                    <p>
                        {{ $class?->name ?? 'Classe' }}
                    </p>
                </div>

                <span class="teacher-status">
                    Active
                </span>
            </div>

        @empty

            <p class="teacher-empty">
                Aucune affectation disponible.
            </p>

        @endforelse

    </div>

</div>

<style>
.teacher-dashboard {
    display: grid;
    gap: 22px;
}

.teacher-welcome {
    padding: 26px;
    border-radius: 16px;
    color: white;
    background: linear-gradient(
        135deg,
        #4f46e5,
        #2563eb
    );
}

.teacher-welcome h2 {
    margin: 0 0 10px;
    font-size: 23px;
}

.teacher-welcome p {
    margin: 0;
    color: #e0e7ff;
}

.teacher-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
}

.teacher-stat {
    padding: 23px;
    background: white;
    border: 1px solid #e5eaf3;
    border-radius: 14px;
}

.teacher-stat span {
    display: block;
    color: #66738b;
    font-size: 13px;
}

.teacher-stat strong {
    display: block;
    margin-top: 12px;
    font-size: 30px;
    color: #4f46e5;
}

.teacher-assignment {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    padding: 17px 0;
    border-bottom: 1px solid #e5eaf3;
}

.teacher-assignment strong {
    color: #17223b;
}

.teacher-assignment p {
    margin: 6px 0 0;
    color: #66738b;
    font-size: 13px;
}

.teacher-status {
    padding: 6px 12px;
    border-radius: 20px;
    background: #dcfce7;
    color: #166534;
    font-size: 12px;
    font-weight: 700;
}

.teacher-empty {
    color: #66738b;
    padding: 20px 0;
}

@media (max-width: 768px) {
    .teacher-stats {
        grid-template-columns: 1fr;
    }

    .teacher-welcome {
        padding: 20px;
    }

    .teacher-welcome h2 {
        font-size: 19px;
    }
}
</style>

@endsection

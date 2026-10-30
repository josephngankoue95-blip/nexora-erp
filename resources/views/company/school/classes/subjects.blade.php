@extends('layouts.school')

@section('title', 'Matières de la classe')
@section('page-title', 'Configuration des matières')

@push('styles')
    @vite(['resources/css/school-class-subjects.css'])
@endpush

@section('content')
<div class="school-subjects-page">
    <header class="school-subjects-header">
        <div>
            <h1>Matières — {{ $class->name }}</h1>
            <p>
                {{ $class->level->name ?? '' }} ·
                {{ $class->level->section->name ?? '' }} ·
                {{ $class->year->name ?? '' }}
            </p>
        </div>
        <a href="{{ route('company.school.classes.index') }}">← Retour aux classes</a>
    </header>

    @if(session('success'))
        <div class="school-subjects-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="school-subjects-error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($class->year->is_archived)
        <p class="school-subjects-error">Année archivée : modifications désactivées.</p>
    @else
        <section class="school-subjects-card">
            <h2>Ajouter une matière à la classe</h2>

            <form method="POST"
                  action="{{ route('company.school.classes.subjects.store', $class->id) }}"
                  class="school-subjects-grid">
                @csrf

                <label>
                    Matière
                    <select name="school_subject_id" required>
                        <option value="">Choisir une matière</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('school_subject_id') == $subject->id)>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label>
                    Groupe
                    <select name="group_number" required>
                        <option value="">Choisir un groupe</option>
                        @foreach([1, 2, 3] as $number)
                            <option value="{{ $number }}" @selected(old('group_number') == $number)>
                                {{ $number }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label>
                    Coefficient
                    <input type="number" name="coefficient" step="0.01" min="0" max="999.99"
                           value="{{ old('coefficient', 1) }}" required>
                </label>

                <label>
                    Ordre bulletin
                    <input type="number" name="sort_order" min="0" max="65535"
                           value="{{ old('sort_order', 0) }}">
                </label>

                <label>
                    Statut
                    <select name="is_active">
                        <option value="1" @selected(old('is_active', '1') == '1')>Active</option>
                        <option value="0" @selected(old('is_active', '1') == '0')>Inactive</option>
                    </select>
                </label>

                <button type="submit">Ajouter la matière</button>
            </form>
        </section>
    @endif

    <section class="school-subjects-card">
        <h2>Matières configurées ({{ $assignments->count() }})</h2>

        @forelse($assignments as $entry)
            <div class="school-subjects-entry">
                <div class="school-subjects-entry-title">
                    <strong>{{ $entry->subject->name ?? 'Matière supprimée' }}</strong>
                    <span>
                        @if($entry->group_number)
                            Groupe {{ $entry->group_number }}
                        @elseif($entry->group)
                            Ancien groupe : {{ $entry->group->name }}
                        @else
                            Sans groupe
                        @endif
                    </span>
                </div>

                @unless($class->year->is_archived)
                    <form method="POST"
                          action="{{ route('company.school.classes.subjects.update', [$class->id, $entry->id]) }}"
                          class="school-subjects-grid">
                        @csrf
                        @method('PUT')

                        <label>
                            Matière
                            <select name="school_subject_id" required>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        @selected(old('school_subject_id', $entry->school_subject_id) == $subject->id)>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label>
                            Groupe
                            <select name="group_number" required>
                                <option value="">Choisir un groupe</option>
                                @foreach([1, 2, 3] as $number)
                                    <option value="{{ $number }}"
                                        @selected(old('group_number', $entry->group_number) == $number)>
                                        {{ $number }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label>
                            Coefficient
                            <input type="number" name="coefficient" step="0.01" min="0" max="999.99"
                                   value="{{ old('coefficient', $entry->coefficient) }}" required>
                        </label>

                        <label>
                            Ordre bulletin
                            <input type="number" name="sort_order" min="0" max="65535"
                                   value="{{ old('sort_order', $entry->sort_order) }}">
                        </label>

                        <label>
                            Statut
                            <select name="is_active">
                                <option value="1" @selected(old('is_active', (string) (int) $entry->is_active) == '1')>Active</option>
                                <option value="0" @selected(old('is_active', (string) (int) $entry->is_active) == '0')>Inactive</option>
                            </select>
                        </label>

                        <button type="submit">Enregistrer</button>
                    </form>

                    <form method="POST"
                          action="{{ route('company.school.classes.subjects.destroy', [$class->id, $entry->id]) }}"
                          onsubmit="return confirm('Retirer cette matière de la classe ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="school-subjects-danger">Retirer</button>
                    </form>
                @else
                    <p>
                        Coefficient : {{ $entry->coefficient }} ·
                        {{ $entry->is_active ? 'Active' : 'Inactive' }}
                    </p>
                @endunless
            </div>
        @empty
            <p>Aucune matière configurée pour cette classe.</p>
        @endforelse
    </section>
</div>
@endsection

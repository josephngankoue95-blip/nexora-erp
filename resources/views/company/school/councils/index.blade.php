@extends('layouts.school')
@section('title', 'Conseils de classe')
@section('page-title', 'Conseils de classe')
@section('page-description', 'Décisions pédagogiques, validation et procès-verbaux')
@push('styles')
    @vite('resources/css/school-councils.css')
@endpush
@section('content')
<div class="council-page">
    <section class="council-panel">
        <h2>Choisir une classe et une période</h2>
        <form method="GET" action="{{ route('company.school.councils.index') }}" class="council-filters">
            <label>Année scolaire
                <select name="year_id" required>
                    @foreach($ctx['years'] as $year)
                        <option value="{{ $year->id }}" @selected($ctx['yearId'] == $year->id)>{{ $year->name ?? $year->label ?? $year->id }}</option>
                    @endforeach
                </select>
            </label>
            <label>Classe
                <select name="class_id" required>
                    <option value="">Choisir une classe</option>
                    @foreach($ctx['classes'] as $class)
                        <option value="{{ $class->id }}" @selected($ctx['classId'] == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Période
                <select name="kind">
                    <option value="sequence" @selected($ctx['kind'] === 'sequence')>Séquence</option>
                    <option value="trimester" @selected($ctx['kind'] === 'trimester')>Trimestre</option>
                    <option value="annual" @selected($ctx['kind'] === 'annual')>Année</option>
                </select>
            </label>
            <label>Numéro
                <select name="number">
                    @for($i=1;$i<=6;$i++)
                        <option value="{{ $i }}" @selected($ctx['number'] === $i)>{{ $i }}</option>
                    @endfor
                </select>
            </label>
            <button class="council-btn" type="submit">Afficher</button>
        </form>
    </section>

    @if($ctx['classId'])
        @php
            $params = ['year_id' => $ctx['yearId'], 'class_id' => $ctx['classId'], 'kind' => $ctx['kind'], 'number' => $ctx['number']];
            $locked = $council && $council->status === 'validated';
        @endphp
        <section class="council-panel">
            <div class="council-header">
                <div>
                    <h2>Décisions du conseil</h2>
                    <p>{{ $students->count() }} élèves inscrits · {{ $locked ? 'Conseil validé' : 'Brouillon' }}</p>
                </div>
                @if($locked)
                    <div class="council-actions">
                        <a class="council-btn" target="_blank" rel="noopener" href="{{ route('company.school.councils.pv', $params + ['order' => 'merit']) }}">PV par mérite</a>
                        <a class="council-btn secondary" target="_blank" rel="noopener" href="{{ route('company.school.councils.pv', $params + ['order' => 'alphabetical']) }}">PV alphabétique</a>
                    </div>
                @endif
            </div>
            <form id="council-save" method="POST" action="{{ route('company.school.councils.save', $params) }}">
                @csrf
                <div class="council-table-wrap">
                    <table class="council-table">
                        <thead><tr><th>Élève</th><th>Moyenne /20</th><th>Rang</th><th>Distinction</th><th>Décision</th><th>Appréciation</th></tr></thead>
                        <tbody>
                        @forelse($students as $student)
                            @php($saved = $decisions->get($student->id))
                            <tr>
                                <td><strong>{{ $student->last_name }} {{ $student->first_name }}</strong><small>{{ $student->student_number }}</small></td>
                                <td>{{ $student->overall === null ? '—' : number_format((float)$student->overall, 2, ',', ' ') }}</td>
                                <td>{{ $student->rank ?? '—' }}</td>
                                <td>
                                    <select name="students[{{ $student->id }}][distinction]" @disabled($locked)>
                                        @foreach(['none'=>'Aucune','honor'=>'Tableau d’honneur','encouragement'=>'Encouragements','congratulations'=>'Félicitations','excellence'=>'Excellence'] as $value=>$label)
                                            <option value="{{ $value }}" @selected(old('students.'.$student->id.'.distinction', $saved->distinction ?? 'none') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="students[{{ $student->id }}][decision]" @disabled($locked)>
                                        @foreach(['none'=>'Aucune','warning_work'=>'Avertissement travail','warning_conduct'=>'Avertissement conduite','blame'=>'Blâme','promotion'=>'Passage','repeat'=>'Redoublement'] as $value=>$label)
                                            <option value="{{ $value }}" @selected(old('students.'.$student->id.'.decision', $saved->decision ?? 'none') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><textarea name="students[{{ $student->id }}][appreciation]" rows="2" @disabled($locked)>{{ old('students.'.$student->id.'.appreciation', $saved->appreciation ?? '') }}</textarea></td>
                            </tr>
                        @empty
                            <tr><td colspan="6">Aucun élève inscrit pour cette classe et cette année.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <label class="council-general">Observation générale du conseil
                    <textarea name="general_observation" rows="3" @disabled($locked)>{{ old('general_observation', $council->general_observation ?? '') }}</textarea>
                </label>
                @if(!$locked && $students->isNotEmpty())
                    <button class="council-btn" type="submit">Enregistrer le brouillon</button>
                @endif
            </form>
            @if(!$locked && $council && $students->isNotEmpty())
                <form method="POST" action="{{ route('company.school.councils.validate', $params) }}" onsubmit="return confirm('Valider définitivement ce conseil de classe ? Les décisions ne seront plus modifiables.');" class="council-validate">
                    @csrf
                    <button class="council-btn danger" type="submit">Valider définitivement le conseil</button>
                </form>
            @endif
        </section>
    @endif
</div>
@endsection

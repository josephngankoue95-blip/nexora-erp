@extends('layouts.school')

@section('title', 'Justificatifs d’absences')

@vite(['resources/css/school-attendance-justifications.css'])

@section('content')
@php
    $statusLabels = ['pending' => 'En attente', 'approved' => 'Validé', 'rejected' => 'Refusé'];
    $attendanceLabels = ['absent' => 'Absent', 'late' => 'Retard', 'excused' => 'Absence excusée'];
@endphp
<div class="saj-page">
    <header class="saj-header">
        <div>
            <div class="saj-eyebrow"><span class="saj-dot"></span> VIE SCOLAIRE <span class="saj-slash">/</span> SUIVI DES ABSENCES</div>
            <h1>Justificatifs d'absence</h1>
            <p>Consultez les absences et retards, soumettez les motifs et suivez les décisions de validation.</p>
        </div>
        <a class="saj-button saj-button-back" href="{{ route('company.school.attendance.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            Retour aux appels
        </a>
    </header>

    @if(session('success'))
        <div class="saj-alert saj-alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="saj-alert saj-alert-error" role="alert"><strong>Veuillez corriger les erreurs :</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <section class="saj-panel saj-filter-panel">
        <div class="saj-section-title">
            <div><span class="saj-kicker">RECHERCHE</span><h2>Filtrer les justificatifs</h2></div>
            <span class="saj-muted">Résultats de votre établissement</span>
        </div>
        <form method="GET" action="{{ route('company.school.attendance.justifications.index') }}" class="saj-filter-form">
            <label class="saj-field"><span>Statut du justificatif</span>
                <select name="status" id="saj-status">
                    <option value="" @selected(!$status)>Tous les statuts</option>
                    <option value="none" @selected($status === 'none')>Sans justificatif</option>
                    @foreach($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="saj-button saj-button-primary">Afficher les résultats</button>
            @if($status)
                <a class="saj-clear" href="{{ route('company.school.attendance.justifications.index') }}">Effacer le filtre</a>
            @endif
        </form>
    </section>

    <section class="saj-results" aria-label="Liste des absences et justificatifs">
        <div class="saj-results-header">
            <div><span class="saj-kicker">DOSSIERS</span><h2>Absences et retards</h2></div>
            <span class="saj-result-count">{{ $rows->total() }} dossier(s)</span>
        </div>
        <div class="saj-card-list">
            @forelse($rows as $row)
                @php
                    $justificationStatus = $row->justification_status;
                    $badgeClass = $justificationStatus === 'approved' ? 'approved' : ($justificationStatus === 'rejected' ? 'rejected' : ($justificationStatus === 'pending' ? 'pending' : 'none'));
                    $studentName = trim($row->last_name.' '.$row->first_name);
                @endphp
                <article class="saj-card">
                    <div class="saj-card-top">
                        <div class="saj-student">
                            <span class="saj-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($row->last_name, 0, 1).mb_substr($row->first_name, 0, 1)) }}</span>
                            <div><h3>{{ $studentName }}</h3>
                                <div class="saj-meta"><span>{{ $row->class_name }}</span><span class="saj-meta-dot">•</span><span>{{ \Illuminate\Support\Carbon::parse($row->attendance_date)->format('d/m/Y') }}</span><span class="saj-meta-dot">•</span><span>{{ $attendanceLabels[$row->attendance_status] ?? ucfirst($row->attendance_status) }}</span></div>
                            </div>
                        </div>
                        <span class="saj-badge saj-badge-{{ $badgeClass }}">{{ $statusLabels[$justificationStatus] ?? 'Sans justificatif' }}</span>
                    </div>
                    @if($row->reason || $row->review_note)
                        <div class="saj-details">
                            @if($row->reason)<div><strong>Motif fourni</strong><p>{{ $row->reason }}</p></div>@endif
                            @if($row->review_note)<div><strong>Avis de validation</strong><p>{{ $row->review_note }}</p></div>@endif
                        </div>
                    @endif
                    @if(!$justificationStatus || $justificationStatus === 'pending')
                        <form method="POST" action="{{ route('company.school.attendance.justifications.submit', $row->id) }}" enctype="multipart/form-data" class="saj-submit-form">
                            @csrf
                            <h4>{{ $justificationStatus === 'pending' ? 'Modifier le justificatif en attente' : 'Soumettre un justificatif' }}</h4>
                            <label class="saj-field" for="saj-reason-{{ $row->id }}"><span>Motif de l'absence ou du retard <em>*</em></span>
                                <textarea id="saj-reason-{{ $row->id }}" name="reason" rows="3" maxlength="3000" required placeholder="Décrivez le motif de l'absence…">{{ old('reason', $row->reason) }}</textarea>
                            </label>
                            <div class="saj-form-footer">
                                <label class="saj-field saj-file" for="saj-file-{{ $row->id }}"><span>Pièce justificative <small>(facultative)</small></span>
                                    <input id="saj-file-{{ $row->id }}" type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png">
                                    <small>PDF, JPG ou PNG — 4 Mo maximum</small>
                                </label>
                                <button class="saj-button saj-button-primary" type="submit">Soumettre le justificatif</button>
                            </div>
                        </form>
                    @endif
                    @if($canReview && $justificationStatus === 'pending')
                        <form method="POST" action="{{ route('company.school.attendance.justifications.review', $row->id) }}" class="saj-review-form">
                            @csrf
                            <label class="saj-field" for="saj-review-{{ $row->id }}"><span>Décision de la direction / surveillance</span>
                                <input id="saj-review-{{ $row->id }}" type="text" name="review_note" maxlength="2000" placeholder="Commentaire de validation (facultatif)">
                            </label>
                            <div class="saj-review-actions">
                                <button type="submit" name="decision" value="approved" class="saj-button saj-button-approve">Valider</button>
                                <button type="submit" name="decision" value="rejected" class="saj-button saj-button-reject">Refuser</button>
                            </div>
                        </form>
                    @endif
                </article>
            @empty
                <div class="saj-empty"><div class="saj-empty-icon" aria-hidden="true">▤</div><h3>Aucun dossier trouvé</h3><p>Il n'y a aucune absence ni aucun retard correspondant à ce filtre.</p><a class="saj-button saj-button-back" href="{{ route('company.school.attendance.justifications.index') }}">Voir tous les dossiers</a></div>
            @endforelse
        </div>
        @if($rows->hasPages())<div class="saj-pagination">{{ $rows->links() }}</div>@endif
    </section>
</div>
@endsection

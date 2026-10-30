<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Procès-verbal du conseil de classe</title>
<style>
@page { size: A4 landscape; margin: 12mm; }
* { box-sizing: border-box; }
body { font-family: Arial, sans-serif; color: #182333; font-size: 11px; }
header { display: flex; justify-content: space-between; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 12px; }
h1 { margin: 0 0 7px; font-size: 19px; text-align: center; }
h2 { font-size: 12px; margin: 5px 0; }
.meta { display: flex; justify-content: space-between; margin: 12px 0; }
table { width: 100%; border-collapse: collapse; }
th, td { border: 1px solid #777; padding: 6px; text-align: left; vertical-align: top; }
th { background: #edf2ff; }
tr { break-inside: avoid; }
.stats { margin-top: 14px; }
.signatures { display: flex; justify-content: space-between; margin-top: 35px; text-align: center; }
.signatures div { width: 30%; }
@media print { body { print-color-adjust: exact; -webkit-print-color-adjust: exact; } }
</style>
</head>
<body>
<header>
    <div><strong>RÉPUBLIQUE DU CAMEROUN</strong><div>Paix – Travail – Patrie</div></div>
    <div style="text-align:center"><strong>{{ auth()->user()?->company?->name ?? 'Établissement scolaire' }}</strong><div>Conseil de classe — document validé</div></div>
    <svg width="69" height="46" viewBox="0 0 90 60" aria-label="Drapeau du Cameroun"><rect width="30" height="60" fill="#007a5e"/><rect x="30" width="30" height="60" fill="#ce1126"/><rect x="60" width="30" height="60" fill="#fcd116"/><polygon points="45,13 49,25 62,25 52,33 56,46 45,38 34,46 38,33 28,25 41,25" fill="#fcd116"/></svg>
</header>
<h1>PROCÈS-VERBAL DU CONSEIL DE CLASSE</h1>
<div class="meta">
    <span>Année : <strong>{{ $ctx['years']->firstWhere('id', $ctx['yearId'])->name ?? $ctx['yearId'] }}</strong></span>
    <span>Classe : <strong>{{ $ctx['classes']->firstWhere('id', $ctx['classId'])->name ?? $ctx['classId'] }}</strong></span>
    <span>Période : <strong>{{ ucfirst($ctx['kind']) }} {{ $ctx['kind'] === 'annual' ? '' : $ctx['number'] }}</strong></span>
    <span>Classement : <strong>{{ $order === 'merit' ? 'Par mérite' : 'Alphabétique' }}</strong></span>
</div>
<table>
<thead><tr><th>N°</th><th>Nom et prénom</th><th>Matricule</th><th>Moyenne</th><th>Rang</th><th>Distinction</th><th>Décision</th><th>Appréciation</th></tr></thead>
<tbody>
@foreach($students as $i => $student)
@php
$d = $student->councilDecision;
$distinctions = ['honor'=>'Tableau d’honneur','encouragement'=>'Encouragements','congratulations'=>'Félicitations','excellence'=>'Excellence'];
$decisions = ['none'=>'—','warning_work'=>'Avert. travail','warning_conduct'=>'Avert. conduite','blame'=>'Blâme','promotion'=>'Passage','repeat'=>'Redoublement'];
@endphp
<tr>
<td>{{ $i + 1 }}</td><td>{{ $student->last_name }} {{ $student->first_name }}</td><td>{{ $student->student_number }}</td>
<td>{{ $student->overall === null ? '—' : number_format((float)$student->overall, 2, ',', ' ') }}</td><td>{{ $student->rank ?? '—' }}</td>
<td>{{ $distinctions[$d->distinction ?? ''] ?? '—' }}</td><td>{{ $decisions[$d->decision ?? 'none'] ?? '—' }}</td><td>{{ $d->appreciation ?? '' }}</td>
</tr>
@endforeach
</tbody>
</table>
<p class="stats"><strong>Statistiques :</strong> Effectif : {{ $stats['total'] }} — Élèves notés : {{ $stats['graded'] }} — Moyenne de classe : {{ $stats['average'] === null ? '—' : number_format((float)$stats['average'], 2, ',', ' ') }}/20 — Élèves avec moyenne ≥ 10 : {{ $stats['passed'] }} — Taux de réussite : {{ $stats['graded'] ? number_format(100*$stats['passed']/$stats['graded'], 1, ',', ' ') : '0' }} %</p>
<p><strong>Observation générale :</strong> {{ $council->general_observation ?? '—' }}</p>
<div class="signatures"><div>Le professeur principal<br><br>________________________</div><div>Le secrétaire du conseil<br><br>________________________</div><div>Le chef d'établissement<br><br>________________________</div></div>
</body>
</html>

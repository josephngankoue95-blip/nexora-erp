<!DOCTYPE html><html lang="fr"><head><meta charset="utf-8"><title>Emploi du temps</title><style>
@page{size:A4 landscape;margin:12mm}body{font-family:Arial,sans-serif;color:#17223b}h1{text-align:center;font-size:22px}p{text-align:center}table{width:100%;border-collapse:collapse;margin-top:22px;font-size:12px}th,td{border:1px solid #667085;padding:9px;text-align:left}th{background:#f1f5f9}.sign{margin-top:40px;text-align:right}
</style></head><body>
<h1>EMPLOI DU TEMPS</h1>
<p>{{ auth()->user()?->company?->name ?? 'Établissement' }}</p>
<p>Classe : {{ $classes->firstWhere('id', $classId)?->name ?? '—' }} | Année : {{ $years->firstWhere('id', $yearId)?->name ?? $years->firstWhere('id', $yearId)?->label ?? $yearId }}</p>
@php $days = [1=>'Lundi',2=>'Mardi',3=>'Mercredi',4=>'Jeudi',5=>'Vendredi',6=>'Samedi']; @endphp
<table><thead><tr><th>Jour</th><th>Heure</th><th>Matière</th><th>Salle</th><th>Remarque</th></tr></thead><tbody>@forelse($slots as $slot)<tr><td>{{ $days[$slot->weekday] ?? $slot->weekday }}</td><td>{{ substr($slot->starts_at,0,5) }} – {{ substr($slot->ends_at,0,5) }}</td><td>{{ $slot->subject_name }}</td><td>{{ $slot->room_name ?? '—' }}</td><td>{{ $slot->note ?? '—' }}</td></tr>@empty<tr><td colspan="5">Aucun cours planifié.</td></tr>@endforelse</tbody></table>
<div class="sign">Visa de la direction : __________________</div>
</body></html>

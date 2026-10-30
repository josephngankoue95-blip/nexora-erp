<!doctype html>
<html lang="fr"><head><meta charset="utf-8"><title>Accès scolaires</title>
<style>body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#1f2937}h1{font-size:20px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #cbd5e1;padding:7px;text-align:left;word-break:break-word}th{background:#eef2ff}</style>
</head><body><h1>Comptes scolaires — export administratif</h1>
<p>Généré le {{ now()->format('d/m/Y H:i') }}. Aucun mot de passe n'est inclus.</p>
<table><thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Statut</th></tr></thead><tbody>
@foreach($rows as $row)<tr>@foreach($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>@endforeach
</tbody></table></body></html>

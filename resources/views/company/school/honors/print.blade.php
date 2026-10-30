<!doctype html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Tableaux d'honneur — {{ $class->name }}</title>
<style>
@page{size:A4 portrait;margin:13mm}*{box-sizing:border-box}body{font-family:Georgia,'Times New Roman',serif;margin:0;color:#15233a;background:#fff}.certificate{width:100%;min-height:260mm;padding:12mm 9mm;page-break-after:always;break-after:page;border:6px double #b48b30;display:flex;flex-direction:column;align-items:center;text-align:center}.certificate:last-child{page-break-after:auto;break-after:auto}.head{width:100%;display:flex;align-items:center;justify-content:space-between;gap:12px}.state{font-size:12px;font-weight:bold;line-height:1.6}.flag{width:90px;height:60px;flex-shrink:0}.school{margin-top:25px;font-size:17px;font-weight:bold;text-transform:uppercase}.motto{font-size:12px}.title{margin:45px 0 8px;font-size:31px;letter-spacing:1px;color:#94681a}.sub{font-size:16px}.name{font-size:28px;margin:35px 0 16px;font-weight:bold}.detail{font-size:17px;line-height:2}.distinction{font-size:22px;font-weight:bold;color:#94681a;margin-top:22px}.sign{margin-top:auto;width:100%;display:flex;justify-content:space-between;padding:0 15px 22px;font-size:13px}.empty{padding:40px;text-align:center}@media print{.certificate{min-height:260mm}}
</style></head><body>
@forelse($students as $student)
<section class="certificate">
<div class="head"><div class="state">RÉPUBLIQUE DU CAMEROUN<br><span class="motto">Paix – Travail – Patrie</span></div>
<svg class="flag" viewBox="0 0 90 60" role="img" aria-label="Drapeau du Cameroun" xmlns="http://www.w3.org/2000/svg"><rect width="30" height="60" fill="#007A5E"/><rect x="30" width="30" height="60" fill="#CE1126"/><rect x="60" width="30" height="60" fill="#FCD116"/><polygon points="45,14 48.8,25.5 61,25.5 51,32.8 54.8,44.5 45,37.2 35.2,44.5 39,32.8 29,25.5 41.2,25.5" fill="#FCD116"/></svg>
</div>
<div class="school">{{ $company->name }}</div><div>Année scolaire : {{ $year->name }}</div>
<h1 class="title">TABLEAU D'HONNEUR</h1><div class="sub">{{ $kind==='annual'?'Année entière':($kind==='sequence'?'Séquence ':'Trimestre ').$number }}</div>
<div class="name">{{ $student->first_name }} {{ $student->last_name }}</div>
<div class="detail">Matricule : {{ $student->student_number }}<br>Classe : {{ $class->name }}<br>Moyenne générale : <strong>{{ number_format($student->overall,2,',',' ') }} / 20</strong><br>Rang : <strong>{{ $student->rank }}</strong></div>
<div class="distinction">{{ $student->distinction }}</div>
<div class="sign"><span>Fait le : ____ / ____ / ______</span><span>Le chef d'établissement<br><br>Signature et cachet</span></div>
</section>
@empty <p class="empty">Aucun élève n'atteint les seuils de distinction pour cette sélection.</p> @endforelse
</body></html>

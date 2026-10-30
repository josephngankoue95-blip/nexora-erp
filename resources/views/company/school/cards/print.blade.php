<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cartes scolaires — {{ $year->name }}</title>
<style>
@page { size: A4 portrait; margin: 10mm; }
* { box-sizing: border-box; }
body { margin:0; background:#e8edf5; color:#14243d; font-family:DejaVu Sans, sans-serif; }
.preview-toolbar { max-width:190mm; margin:15px auto 0; padding:12px 16px; background:#fff; border-radius:10px; font:13px Arial,sans-serif; color:#334155; }
.preview-toolbar button { float:right; border:0; border-radius:6px; padding:8px 14px; background:#143e70; color:white; cursor:pointer; }
.page { width:190mm; margin:8mm auto; font-size:0; }
/* Recto uniquement : 2 cartes par ligne, jusqu'à 4 lignes par page A4. */
.card { display:inline-block; vertical-align:top; width:85.6mm; height:53.98mm; margin:0 4mm 4mm 0; background:white; border:.25mm solid #b7c5d7; border-radius:2.2mm; overflow:hidden; position:relative; page-break-inside:avoid; font-size:8px; }
.card-top { height:13.5mm; position:relative; background:#123a69; color:white; text-align:center; padding:1.4mm 12.3mm .8mm; border-bottom:1mm solid #e0b847; }
.header-symbol { position:absolute; top:2.2mm; width:9mm; height:8.3mm; background:white; border-radius:1mm; padding:.45mm; text-align:center; }
.header-symbol.left { left:2mm; }
.header-symbol.right { right:2mm; }
.header-symbol img { max-width:100%; max-height:100%; width:auto; height:auto; }
.header-symbol.flag img { width:100%; height:100%; }
.school-name { font-size:8.1px; font-weight:bold; line-height:1.16; max-height:7mm; overflow:hidden; }
.school-year { font-size:6.1px; font-weight:bold; letter-spacing:.2px; margin-top:.5mm; }
.motto { font-size:5.2px; margin-top:.3mm; white-space:nowrap; overflow:hidden; }
.card-ribbon { height:3.3mm; padding:.5mm 2.4mm; color:#123a69; background:#f0f5fb; font-size:6.3px; font-weight:bold; text-align:center; letter-spacing:.3px; }
.card-body { display:table; table-layout:fixed; width:100%; padding:1.5mm 2.5mm 0; }
.photo-wrap { display:table-cell; width:19.5mm; vertical-align:top; }
.photo, .photo-placeholder { width:18mm; height:22.5mm; border:1px solid #b6c5d8; border-radius:1mm; }
.photo { object-fit:cover; }
.photo-placeholder { background:#f0f4f9; color:#78899d; font-size:7px; line-height:22.5mm; text-align:center; }
.details { display:table-cell; vertical-align:top; padding-left:1mm; }
.detail-line { line-height:1.17; margin-bottom:.55mm; }
.label { color:#627187; font-size:6.2px; }
.value { color:#182d48; font-size:7.1px; font-weight:bold; overflow-wrap:break-word; }
.value.name { font-size:7.6px; }
.value.number { color:#0f5084; }
.card-bottom { position:absolute; left:2.5mm; right:2.5mm; bottom:1.3mm; height:9.3mm; border-top:.2mm solid #d9e2ed; }
.signature-area { position:absolute; left:0; bottom:0; width:43mm; height:8.5mm; text-align:center; }
.signature-space { height:5.4mm; }
.signature-label { display:block; margin:0 5mm; border-top:.2mm solid #9dadc0; padding-top:.4mm; font-size:5.3px; color:#53647c; }
.stamp { position:absolute; left:34mm; bottom:.1mm; max-width:14mm; max-height:9.8mm; }
.qr { position:absolute; right:0; bottom:0; width:9.5mm; height:9.5mm; }
.qr img { width:9.5mm; height:9.5mm; }
.verify-note { position:absolute; right:10.5mm; bottom:2mm; font-size:4.6px; color:#72829a; }
@media print { body { background:white; } .preview-toolbar { display:none; } .page { margin:0; width:190mm; } .card { border-color:#a9b9ca; } }
</style>
</head>
<body>
@php
    $pdfMode = request()->boolean('pdf');
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    $storageAsset = function (?string $path) use ($disk, $pdfMode) {
        if (!$path) return null;
        $relative = str_replace('\\', '/', trim($path));
        $relative = preg_replace('#^(?:/?storage/|/?public/)#', '', $relative);
        $relative = ltrim($relative, '/');
        if ($relative === '' || str_contains($relative, '..') || !$disk->exists($relative)) return null;
        if ($pdfMode) {
            $mime = $disk->mimeType($relative);
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'], true)) return null;
            return 'data:' . $mime . ';base64,' . base64_encode($disk->get($relative));
        }
        return $disk->url($relative);
    };
    $logoUrl = $storageAsset($schoolLogo ?? null);
    $stampUrl = $storageAsset($schoolStamp ?? null);
    $flagPath = public_path('images/cameroon-flag.png');
    $flagUrl = is_file($flagPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($flagPath))
        : null;
@endphp
@if(!$pdfMode)
<div class="preview-toolbar">
    <button type="button" onclick="window.print()">Imprimer les cartes</button>
    <strong>Aperçu — {{ $year->name }}</strong> · {{ $cards->count() }} carte(s) · Recto uniquement
</div>
@endif
<div class="page">
@foreach($cards as $student)
@php
    $photoUrl = $storageAsset($student->card_photo ?? null);
    $birthDate = !empty($student->date_of_birth)
        ? \Illuminate\Support\Carbon::parse($student->date_of_birth)->format('d/m/Y') : '—';
    $gender = strtolower(trim((string) ($student->gender ?? '')));
    $genderLabel = match ($gender) {
        'm', 'male', 'masculin', 'homme' => 'M',
        'f', 'female', 'feminin', 'féminin', 'femme' => 'F',
        default => ($student->gender ?? null) ?: '—',
    };
    $qr = null;
    if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
        try {
            $qr = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                ->size(140)->margin(0)->generate($student->verification_url));
        } catch (\Throwable $e) { $qr = null; }
    }
@endphp
<div class="card">
    <div class="card-top">
        <div class="header-symbol left">
            @if($logoUrl)<img src="{{ $logoUrl }}" alt="Logo de l'école">@endif
        </div>
        <div class="header-symbol right flag">
            @if($flagUrl)<img src="{{ $flagUrl }}" alt="Drapeau du Cameroun">@endif
        </div>
        <div class="school-name">{{ mb_strtoupper($schoolName) }}</div>
        <div class="school-year">ANNÉE SCOLAIRE {{ $year->name }}</div>
        @if(!empty($motto))<div class="motto">{{ $motto }}</div>@endif
    </div>
    <div class="card-ribbon">CARTE D'IDENTITÉ SCOLAIRE</div>
    <div class="card-body">
        <div class="photo-wrap">
            @if($photoUrl)<img class="photo" src="{{ $photoUrl }}" alt="Photo de l'élève">
            @else<div class="photo-placeholder">PHOTO</div>@endif
        </div>
        <div class="details">
            <div class="detail-line"><span class="label">Nom : </span><span class="value name">{{ $student->last_name }}</span></div>
            <div class="detail-line"><span class="label">Prénom : </span><span class="value name">{{ $student->first_name }}</span></div>
            <div class="detail-line"><span class="label">Matricule : </span><span class="value number">{{ $student->student_number }}</span></div>
            <div class="detail-line"><span class="label">Classe : </span><span class="value">{{ $student->class_name }}</span></div>
            <div class="detail-line"><span class="label">Né(e) le : </span><span class="value">{{ $birthDate }}</span></div>
            <div class="detail-line"><span class="label">Lieu : </span><span class="value">{{ $student->place_of_birth ?? '—' }}</span></div>
            <div class="detail-line"><span class="label">Sexe : </span><span class="value">{{ $genderLabel }}</span></div>
        </div>
    </div>
    <div class="card-bottom">
        <div class="signature-area">
            <div class="signature-space"></div>
            <span class="signature-label">{{ $principalName ?: 'Signature du responsable' }}</span>
        </div>
        @if($stampUrl)<img class="stamp" src="{{ $stampUrl }}" alt="Cachet officiel">@endif
        @if($qr)
            <div class="verify-note">Vérification</div>
            <div class="qr"><img src="data:image/png;base64,{{ $qr }}" alt="QR de vérification"></div>
        @endif
    </div>
</div>
@endforeach
</div>
</body>
</html>

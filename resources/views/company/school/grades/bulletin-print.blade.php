
<!DOCTYPE html>
<html lang="{{ $english ? 'en' : 'fr' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>
    {{ $title }} — {{ $student->last_name }} {{ $student->first_name }}
</title>

<style>
@page {
    size: A4 portrait;
    margin: 0;
}

* {
    box-sizing: border-box;
}

html, body {
    margin: 0;
    padding: 0;
}

body {
    background: #e8edf4;
    color: #192b44;
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 8px;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

.sheet {
    width: 210mm;
    min-height: 297mm;
    padding: 8mm 8mm 7mm;
    margin: 18px auto 30px;
    background: #fff;
    box-shadow: 0 12px 40px rgba(15, 40, 75, .17);
}

.top,
.info,
.marks,
.mini,
.signatures {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.top {
    margin-bottom: 2mm;
}

.top td {
    width: 36%;
    text-align: center;
    vertical-align: middle;
    font-size: 7px;
    line-height: 1.55;
    color: #243650;
}

.top td.center-logo {
    width: 28%;
}

.top strong {
    color: #122f57;
    font-weight: bold;
}

.logo {
    display: block;
    margin: 0 auto 4px;
    width: 62px;
    height: 62px;
    object-fit: contain;
}

.logo-placeholder {
    margin: 0 auto 4px;
    border: 2px solid #164b85;
    color: #164b85;
    border-radius: 50%;
    width: 62px;
    height: 62px;
    line-height: 58px;
    font-size: 24px;
    font-weight: bold;
    text-align: center;
}

.motto {
    font-size: 6px;
    font-weight: bold;
    color: #41658e;
}

.year {
    margin-top: 4px;
    padding: 6px 4px;
    border-top: 2px solid #123b70;
    border-bottom: 1px solid #123b70;
    text-align: center;
    font-size: 9px;
    font-weight: bold;
    color: #123b70;
    background: #f5f8fc;
}

.report-title {
    margin: 6px 0 7px;
    border: 1.5px solid #123b70;
    background: #e8f0fa;
    text-align: center;
    padding: 8px 5px;
    color: #123b70;
    font-size: 10px;
    font-weight: bold;
    text-transform: uppercase;
}

.info {
    margin-bottom: 7px;
}

.info td {
    border: 1px solid #aab8ca;
    padding: 5px 6px;
    vertical-align: middle;
    font-size: 7px;
    line-height: 1.35;
}

.info .key {
    width: 18%;
    background: #edf3fa;
    color: #123b70;
    font-weight: bold;
}

.info .value {
    width: 32%;
    color: #1d2939;
}

.marks {
    margin-top: 6px;
}

.marks thead {
    display: table-header-group;
}

.marks th {
    border: 1px solid #31567e;
    background: #123b70;
    color: #fff;
    padding: 7px 3px;
    font-size: 6.5px;
    line-height: 1.35;
    text-align: center;
    font-weight: bold;
}

.marks th:first-child {
    text-align: left;
    padding-left: 7px;
}

.marks td {
    border: 1px solid #b8c6d6;
    padding: 5px 3px;
    font-size: 7px;
    line-height: 1.3;
    text-align: center;
}

.marks tbody tr:nth-child(even):not(.group):not(.total) td {
    background: #f8fafc;
}

.marks td.subject {
    text-align: left;
    font-weight: bold;
    padding-left: 7px;
    color: #243a57;
}

.marks .group td {
    background: #dbe9f8;
    color: #123b70;
    font-weight: bold;
    text-align: center;
    padding: 5px 4px;
    border-top: 1px solid #9db4d0;
}

.marks .total td {
    background: #123b70;
    color: #fff;
    font-weight: bold;
    padding: 6px 4px;
    border-color: #557394;
}

.marks .positive {
    color: #087a43;
    font-weight: bold;
}

.marks .negative {
    color: #b42332;
    font-weight: bold;
}

.summary {
    width: 100%;
    margin-top: 8px;
    border-spacing: 4px 0;
    border-collapse: separate;
    table-layout: fixed;
}

.summary > tbody > tr > td {
    border: 1px solid #aabbd0;
    width: 25%;
    vertical-align: top;
    padding: 0;
    background: #fff;
}

.panel-title {
    background: #123b70;
    color: white;
    text-align: center;
    padding: 7px 3px;
    font-weight: bold;
    font-size: 7px;
    line-height: 1.5;
    min-height: 28px;
}

.mini td {
    border-bottom: 1px solid #d6e0eb;
    padding: 5px 4px;
    font-size: 6.3px;
    line-height: 1.3;
    vertical-align: middle;
}

.mini tr:last-child td {
    border-bottom: 0;
}

.mini td:first-child {
    background: #edf3fa;
    width: 65%;
    color: #334762;
}

.mini td:last-child {
    font-weight: bold;
    text-align: center;
    color: #123b70;
}

.appreciation {
    margin: 7px 5px;
    padding: 7px 4px;
    border: 1px solid #e7ad35;
    background: #fff4d7;
    color: #6c4905;
    text-align: center;
    font-size: 10px;
    font-weight: bold;
}

.decision {
    padding: 14px 7px;
    text-align: center;
    color: #52657c;
    font-size: 7px;
    line-height: 1.65;
}

.signatures {
    margin-top: 9px;
}

.signatures td {
    width: 33.333%;
    border: 1px solid #8b9bb0;
    text-align: center;
    vertical-align: top;
    height: 23mm;
    padding: 7px 4px;
    font-size: 7px;
    font-weight: bold;
    color: #123b70;
}

.signatures small {
    display: block;
    font-size: 6px;
    font-weight: normal;
    margin-top: 4px;
    color: #53677f;
}

.school-stamp {
    display: block;
    width: 65px;
    height: 65px;
    max-width: 100%;
    object-fit: contain;
    margin: 4px auto;
}

.school-signatory {
    display: block;
    margin-top: 5px;
    font-size: 7px;
    font-weight: bold;
    color: #123b70;
}

.school-signature-date {
    display: block;
    margin-top: 5px;
    font-size: 6px;
    font-weight: normal;
    color: #53677f;
}

.legend {
    font-size: 6px;
    margin-top: 7px;
    line-height: 1.7;
    color: #4d5d72;
    border-top: 1px solid #d7e0ea;
    padding-top: 5px;
}

.legend strong {
    color: #123b70;
}

.footer {
    text-align: center;
    font-size: 6px;
    margin-top: 7px;
    color: #657b95;
    line-height: 1.65;
}

@media screen and (max-width: 850px) {
    body {
        overflow-x: auto;
    }

    .sheet {
        margin: 10px auto;
    }
}

@media print {
    html, body {
        width: 210mm;
        background: white !important;
    }

    .sheet {
        width: 210mm;
        min-height: 0;
        margin: 0;
        padding: 8mm 8mm 7mm;
        box-shadow: none;
    }

    .summary,
    .signatures,
    .marks tr,
    .info tr {
        page-break-inside: avoid;
    }
}
</style>
</head>

<body>

@php
    $rowsCollection = collect($rows ?? []);

    $grouped = $rowsCollection->groupBy(function ($row) {
        $group = (int) ($row['group'] ?? 0);

        return in_array($group, [1, 2, 3], true)
            ? $group
            : 0;
    });

    $companyName = $schoolDocument['name']
        ?? $company->name
        ?? 'Établissement scolaire';

    $schoolMotto = $schoolDocument['motto']
        ?: 'Excellence · Discipline · Réussite';

    $schoolCity = $schoolDocument['city'] ?? null;
    $schoolRegion = $schoolDocument['region'] ?? null;
    $schoolPhone = $schoolDocument['phone'] ?? null;
    $schoolEmail = $schoolDocument['email'] ?? null;

    $principalName = $schoolDocument['principal'] ?? null;
    $directorName = $schoolDocument['director'] ?? null;
    $schoolFooter = $schoolDocument['footer'] ?? null;

    // Images incorporées pour le navigateur et DomPDF.
    $documentImage = function (?string $path): ?string {
        if (!$path || !is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path);

        if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
            return null;
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        return 'data:' . $mime . ';base64,' .
            base64_encode($contents);
    };

    $logoUrl = $documentImage(
        $schoolDocument['logo_path'] ?? null
    );

    $stampUrl = $documentImage(
        $schoolDocument['stamp_path'] ?? null
    );

    $scoreLabel = function ($value, $decimals = 2) {
        return $value === null
            ? '—'
            : number_format(
                (float) $value,
                $decimals,
                '.',
                ''
            );
    };

    $generalRemark = '—';

    if ($overall !== null) {
        if ($overall >= 16) {
            $generalRemark = 'EXCELLENT';
        } elseif ($overall >= 14) {
            $generalRemark = $english ? 'VERY GOOD' : 'TRÈS BIEN';
        } elseif ($overall >= 12) {
            $generalRemark = $english ? 'GOOD' : 'BIEN';
        } elseif ($overall >= 10) {
            $generalRemark = $english ? 'SATISFACTORY' : 'PASSABLE';
        } else {
            $generalRemark = $english
                ? 'NEEDS IMPROVEMENT'
                : 'INSUFFISANT';
        }
    }

    $gender = strtolower(
        trim((string) ($student->gender ?? ''))
    );

    $genderLabel = in_array(
        $gender,
        ['f', 'female', 'féminin', 'feminin'],
        true
    )
        ? 'F / FEMALE'
        : (
            in_array(
                $gender,
                ['m', 'male', 'masculin'],
                true
            )
                ? 'M / MALE'
                : '—'
        );

    $periodLabel = $kind === 'annual'
        ? 'BULLETIN ANNUEL / ANNUAL REPORT'
        : (
            $kind === 'sequence'
                ? 'BULLETIN SÉQUENTIEL / SEQUENTIAL REPORT — SÉQUENCE ' .
                    $number .
                    ' (TRIMESTRE ' .
                    (int) ceil($number / 2) .
                    ')'
                : 'BULLETIN TRIMESTRIEL / TERM REPORT — TRIMESTRE ' .
                    $number
        );

    $classmateCount = (int) ($classSize ?? 0);
    $availableCount = (int) ($gradedCount ?? 0);

    $passedSubjects = $rowsCollection
        ->filter(fn ($r) =>
            ($r['average'] ?? null) !== null &&
            $r['average'] >= 10
        )
        ->count();

    $assessedSubjects = $rowsCollection
        ->filter(fn ($r) =>
            ($r['average'] ?? null) !== null
        )
        ->count();
@endphp

<div class="sheet">

    {{-- EN-TÊTE OFFICIEL --}}
    <table class="top">
        <tr>
            <td>
                <strong>RÉPUBLIQUE DU CAMEROUN</strong><br>
                Paix – Travail – Patrie<br>
                MINISTÈRE DES ENSEIGNEMENTS SECONDAIRES<br>

                <strong>{{ mb_strtoupper($companyName) }}</strong>

                @if($schoolCity || $schoolRegion)
                    <br>
                    {{ collect([$schoolCity, $schoolRegion])
                        ->filter()->implode(' — ') }}
                @endif

                @if($schoolPhone)
                    <br>Tél. : {{ $schoolPhone }}
                @endif
            </td>

            <td class="center-logo">
                @if($logoUrl)
                    <img
                        class="logo"
                        src="{{ $logoUrl }}"
                        alt="Logo de l'établissement"
                    >
                @else
                    <div class="logo-placeholder">
                        {{ mb_strtoupper(mb_substr($companyName, 0, 1)) }}
                    </div>
                @endif

                <div class="motto">
                    {{ $schoolMotto }}
                </div>
            </td>

            <td>
                <strong>REPUBLIC OF CAMEROON</strong><br>
                Peace – Work – Fatherland<br>
                MINISTRY OF SECONDARY EDUCATION<br>

                <strong>{{ mb_strtoupper($companyName) }}</strong>

                @if($schoolCity || $schoolRegion)
                    <br>
                    {{ collect([$schoolCity, $schoolRegion])
                        ->filter()->implode(' — ') }}
                @endif

                @if($schoolEmail)
                    <br>{{ $schoolEmail }}
                @endif
            </td>
        </tr>
    </table>

    <div class="year">
        ANNÉE SCOLAIRE / ACADEMIC YEAR :
        {{ $year->name }}
    </div>

    <div class="report-title">
        {{ $periodLabel }}
    </div>

    {{-- IDENTITÉ DE L'ÉLÈVE --}}
    <table class="info">
        <tr>
            <td class="key">NOM ET PRÉNOM / FULL NAME</td>
            <td class="value">
                {{ $student->last_name }}
                {{ $student->first_name }}
            </td>

            <td class="key">CLASSE / CLASS</td>
            <td class="value">
                {{ $class->name }}
                · {{ $section->level_name ?? '' }}
            </td>
        </tr>

        <tr>
            <td class="key">MATRICULE / REGISTRATION No.</td>
            <td class="value">{{ $student->student_number }}</td>

            <td class="key">SEXE / SEX</td>
            <td class="value">{{ $genderLabel }}</td>
        </tr>

        <tr>
            <td class="key">DATE DE NAISSANCE / DATE OF BIRTH</td>
            <td class="value">
                {{ $student->date_of_birth
                    ? \Carbon\Carbon::parse($student->date_of_birth)->format('d/m/Y')
                    : '—' }}
            </td>

            <td class="key">REDOUBLANT / REPEATER</td>
            <td class="value">—</td>
        </tr>

        <tr>
            <td class="key">PROFESSEUR PRINCIPAL / FORM MASTER</td>
            <td class="value">—</td>

            <td class="key">EFFECTIF / CLASS SIZE</td>
            <td class="value">{{ $classmateCount }}</td>
        </tr>
    </table>

    {{-- TABLEAU DES NOTES --}}
    <table class="marks">
        <thead>
            <tr>
                <th style="width:27%">
                    Matières / Subjects
                </th>
                <th style="width:11%">
                    Note /20<br>Mark /20
                </th>
                <th style="width:8%">
                    Coef.
                </th>
                <th style="width:13%">
                    Points<br>Mark × Coef
                </th>
                <th style="width:9%">
                    Rang<br>Rank
                </th>
                <th style="width:12%">
                    Moy. classe<br>Class avg
                </th>
                <th style="width:8%">
                    ≥ 10<br>Classe
                </th>
                <th style="width:12%">
                    Appréciation<br>Remarks
                </th>
            </tr>
        </thead>

        <tbody>
            @foreach([1, 2, 3, 0] as $groupNumber)

                @php
                    $entries = $grouped->get(
                        $groupNumber,
                        collect()
                    );
                @endphp

                @if($entries->isNotEmpty())

                    <tr class="group">
                        <td colspan="8">
                            {{ $groupNumber
                                ? 'GROUPE ' .
                                    ['', 'I', 'II', 'III'][$groupNumber] .
                                    ' / GROUP ' .
                                    ['', 'I', 'II', 'III'][$groupNumber]
                                : 'MATIÈRES SANS GROUPE / UNGROUPED SUBJECTS' }}
                        </td>
                    </tr>

                    @foreach($entries as $entry)
                        @php
                            $mark = $entry['average'] ?? null;
                            $remark = '—';

                            if ($mark !== null) {
                                if ($mark >= 16) {
                                    $remark = 'A+';
                                } elseif ($mark >= 14) {
                                    $remark = 'A';
                                } elseif ($mark >= 12) {
                                    $remark = 'B';
                                } elseif ($mark >= 10) {
                                    $remark = 'C';
                                } else {
                                    $remark = 'D';
                                }
                            }
                        @endphp

                        <tr>
                            <td class="subject">
                                {{ $entry['name'] }}
                            </td>

                            <td class="{{ $mark !== null && $mark >= 10
                                ? 'positive'
                                : ($mark !== null ? 'negative' : '') }}">
                                {{ $scoreLabel($mark, 2) }}
                            </td>

                            <td>
                                {{ $scoreLabel(
                                    $entry['coefficient'] ?? null,
                                    2
                                ) }}
                            </td>

                            <td>
                                {{ $scoreLabel(
                                    $entry['points'] ?? null,
                                    2
                                ) }}
                            </td>

                            <td>
                                {{ ($entry['rank'] ?? null) === null
                                    ? '—'
                                    : $entry['rank'] .
                                        ' / ' .
                                        ($entry['graded_count'] ?? 0) }}
                            </td>

                            <td>
                                {{ $scoreLabel(
                                    $entry['class_average'] ?? null,
                                    2
                                ) }}
                            </td>

                            <td>
                                {{ ($entry['graded_count'] ?? 0) > 0
                                    ? ($entry['passed_count'] ?? 0) .
                                        ' / ' .
                                        $entry['graded_count']
                                    : '—' }}
                            </td>

                            <td class="{{ $mark !== null && $mark >= 10
                                ? 'positive'
                                : ($mark !== null ? 'negative' : '') }}">
                                {{ $remark }}
                            </td>
                        </tr>
                    @endforeach

                    @php
                        $validEntries = $entries->filter(
                            fn ($r) =>
                                ($r['average'] ?? null) !== null &&
                                (float) ($r['coefficient'] ?? 0) > 0
                        );

                        $groupCoeff = $validEntries->sum('coefficient');
                        $groupPoints = $validEntries->sum('points');

                        $groupAverage = $groupCoeff > 0
                            ? $groupPoints / $groupCoeff
                            : null;
                    @endphp

                    <tr class="total">
                        <td style="text-align:right">
                            TOTAL
                            {{ $groupNumber
                                ? 'GROUPE ' .
                                    ['', 'I', 'II', 'III'][$groupNumber]
                                : 'SANS GROUPE' }}
                        </td>

                        <td>—</td>
                        <td>{{ $scoreLabel($groupCoeff) }}</td>
                        <td>{{ $scoreLabel($groupPoints) }}</td>

                        <td colspan="2">
                            Moyenne / Average
                        </td>

                        <td colspan="2">
                            {{ $groupAverage === null
                                ? '—'
                                : $scoreLabel($groupAverage) . '/20' }}
                        </td>
                    </tr>

                @endif
            @endforeach

            @if($rowsCollection->isEmpty())
                <tr>
                    <td colspan="8" style="text-align:center;padding:10px">
                        Aucune matière configurée pour cette classe /
                        No subjects configured for this class
                    </td>
                </tr>
            @endif

            <tr class="total">
                <td>
                    MOYENNE GÉNÉRALE / GENERAL AVERAGE
                </td>

                <td>{{ $scoreLabel($overall) }}</td>
                <td>{{ $scoreLabel($coefficients) }}</td>
                <td>{{ $scoreLabel($points) }}</td>

                <td colspan="4">
                    {{ $overall === null
                        ? '—'
                        : $scoreLabel($overall) . '/20' }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- STATISTIQUES --}}
    <table class="summary">
        <tr>
            <td>
                <div class="panel-title">
                    RÉSULTAT DE L'ÉLÈVE<br>
                    STUDENT RESULT
                </div>

                <table class="mini">
                    <tr>
                        <td>Moyenne / Average</td>
                        <td>
                            {{ $overall === null
                                ? '—'
                                : $scoreLabel($overall) . '/20' }}
                        </td>
                    </tr>

                    <tr>
                        <td>Rang / Rank</td>
                        <td>
                            {{ $rank === null
                                ? '—'
                                : $rank . ' / ' . $availableCount }}
                        </td>
                    </tr>

                    <tr>
                        <td>Points / Points</td>
                        <td>{{ $scoreLabel($points) }}</td>
                    </tr>

                    <tr>
                        <td>Coef. total / Total coef.</td>
                        <td>{{ $scoreLabel($coefficients) }}</td>
                    </tr>
                </table>

                <div class="appreciation">
                    {{ $generalRemark }}
                </div>
            </td>

            <td>
                <div class="panel-title">
                    PROFIL DE LA CLASSE<br>
                    CLASS PROFILE
                </div>

                <table class="mini">
                    <tr>
                        <td>Moy. classe / Class avg</td>
                        <td>{{ $scoreLabel($classAverage) }}</td>
                    </tr>
                    <tr>
                        <td>Taux réussite / Pass rate</td>
                        <td>
                            {{ $successRate === null
                                ? '—'
                                : number_format($successRate, 1, '.', '') . '%' }}
                        </td>
                    </tr>
                    <tr>
                        <td>Meilleure / Highest</td>
                        <td>{{ $scoreLabel($highest) }}</td>
                    </tr>
                    <tr>
                        <td>Plus faible / Lowest</td>
                        <td>{{ $scoreLabel($lowest) }}</td>
                    </tr>
                </table>
            </td>

            <td>
                <div class="panel-title">
                    TRAVAIL ET CONDUITE<br>
                    WORK &amp; CONDUCT
                </div>

                <table class="mini">
                    <tr>
                        <td>Tableau d'honneur / Honor roll</td>
                        <td>—</td>
                    </tr>
                    <tr>
                        <td>Encouragements</td>
                        <td>—</td>
                    </tr>
                    <tr>
                        <td>Avertissements</td>
                        <td>—</td>
                    </tr>
                    <tr>
                        <td>Absences / Absences</td>
                        <td>—</td>
                    </tr>
                    <tr>
                        <td>Exclusions</td>
                        <td>—</td>
                    </tr>
                </table>
            </td>

            <td>
                <div class="panel-title">
                    DÉCISION DU CONSEIL<br>
                    CLASS DECISION
                </div>

                <div class="decision">
                    @if($assessedSubjects)
                        {{ $passedSubjects }}
                        /
                        {{ $assessedSubjects }}
                        matière(s) évaluée(s) avec une note ≥ 10/20
                    @else
                        Non renseignée / Not recorded
                    @endif

                    <br><br>

                    <strong>Décision officielle :</strong><br>

                    @if(!empty($councilDecision))
                        {{ $councilDecisionLabel ?: 'Non renseignée' }}
                    @else
                        En attente de validation du conseil
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- SIGNATURES ET CACHET --}}
    <table class="signatures">
        <tr>
            <td>
                SIGNATURE DU PARENT
                <small>PARENT'S SIGNATURE</small>
            </td>

            <td>
                DÉCISION DU CONSEIL DE CLASSE
                <small>CLASS COUNCIL DECISION</small>

                @if(!empty($councilDecision))
                    <span class="school-signatory">
                        {{ $councilDecisionLabel ?: 'Non renseignée' }}
                    </span>
                @else
                    <small>
                        En attente de validation / Pending approval
                    </small>
                @endif
            </td>

            <td>
                SIGNATURE DU CHEF D'ÉTABLISSEMENT
                <small>PRINCIPAL'S SIGNATURE</small>

                @if($stampUrl)
                    <img
                        class="school-stamp"
                        src="{{ $stampUrl }}"
                        alt="Cachet de l'établissement"
                    >
                @endif

                @if($principalName || $directorName)
                    <span class="school-signatory">
                        {{ $principalName ?: $directorName }}
                    </span>
                @endif

                <span class="school-signature-date">
                    {{ now()->format('d/m/Y') }}
                </span>
            </td>
        </tr>
    </table>

    {{-- LÉGENDE --}}
    <div class="legend">
        <strong>Légende / Legend :</strong>
        NA = NON ACQUIS / NOT ACQUIRED
        &nbsp; | &nbsp;
        ECA = EN COURS D'ACQUISITION / IN PROGRESS
        &nbsp; | &nbsp;
        A = ACQUIS / ACQUIRED
        &nbsp; | &nbsp;
        A+ = PARFAITEMENT ACQUIS / PERFECTLY ACQUIRED
    </div>

    {{-- PIED DE PAGE --}}
    <div class="footer">
        @if($schoolFooter)
            {{ $schoolFooter }}
            <br>
        @endif

        Ce bulletin est établi sans rature ni surcharge
        — This report is issued without erasure or overwriting

        <br>

        Bulletin provisoire / Provisional report
        · {{ now()->format('d/m/Y H:i') }}
    </div>

</div>

</body>
</html>

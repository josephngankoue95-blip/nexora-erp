<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fiche de notes — {{ $class->name }} — {{ $assignment->subject_name }}</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #17233b; margin: 0; background: #f1f5f9; font-size: 11px; }
        .sheet { max-width: 273mm; min-height: 186mm; margin: 16px auto; padding: 12mm; background: white; }
        .header { text-align: center; border-bottom: 2px solid #173e70; padding-bottom: 8px; margin-bottom: 12px; }
        .header h1 { font-size: 18px; margin: 4px 0; text-transform: uppercase; }
        .header h2 { font-size: 14px; margin: 4px 0; }
        .meta { display: table; width: 100%; margin-bottom: 12px; }
        .meta > div { display: table-cell; width: 50%; line-height: 1.9; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #475569; padding: 5px 6px; }
        th { background: #e9eff8; text-align: center; }
        td { height: 28px; }
        .num { width: 7%; text-align: center; }
        .mat { width: 17%; }
        .student { width: 28%; }
        .score { text-align: center; }
        .footer { margin-top: 20px; display: flex; justify-content: space-between; gap: 20px; }
        .sign { width: 35%; text-align: center; }
        .sign-space { height: 42px; }
        .empty { text-align: center; padding: 20px; }
        .no-print { max-width: 273mm; margin: 16px auto 0; }
        .no-print button { padding: 9px 15px; background: #173e70; color: white; border: none; border-radius: 6px; cursor: pointer; }
        @media print { body { background: white; } .sheet { margin: 0; padding: 0; max-width: none; min-height: auto; } .no-print { display: none; } thead { display: table-header-group; } tr { break-inside: avoid; page-break-inside: avoid; } }
    </style>
</head>
<body>

    <main class="sheet">
        <div class="header">
            <h1>{{ $company->name }}</h1>
            <h2>FICHE VIERGE DE RELEVÉ DES NOTES</h2>
            <div>Année scolaire : {{ $year->name }}</div>
        </div>
        <div class="meta">
            <div>
                <strong>Classe :</strong> {{ $class->name }}<br>
                <strong>Matière :</strong> {{ $assignment->subject_name }}<br>
                <strong>Coefficient :</strong> {{ $assignment->coefficient }}
            </div>
            <div>
                <strong>Enseignant :</strong> {{ $assignment->teacher_last_name }} {{ $assignment->teacher_first_name }}<br>
                <strong>Période :</strong> {{ $period->name }}<br>
                <strong>Effectif :</strong> {{ $students->count() }} élève(s)
            </div>
        </div>
        <table>
            <thead><tr>
                <th class="num">N°</th><th class="mat">Matricule</th><th class="student">Nom et prénom</th>
                @for($i = 1; $i <= $columns; $i++)
                    <th class="score">Note {{ $i }}</th>
                @endfor
            </tr></thead>
            <tbody>
                @forelse($students as $student)
                    <tr><td class="num">{{ $loop->iteration }}</td><td>{{ $student->student_number }}</td><td>{{ $student->last_name }} {{ $student->first_name }}</td>
                        @for($i = 1; $i <= $columns; $i++)<td></td>@endfor
                    </tr>
                @empty
                    <tr><td colspan="{{ 3 + $columns }}" class="empty">Aucun élève inscrit dans cette classe.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="footer">
            <div><strong>Date :</strong> ____ / ____ / ________</div>
            <div class="sign"><strong>Signature de l'enseignant</strong><div class="sign-space"></div>________________________</div>
        </div>
    </main>
</body>
</html>

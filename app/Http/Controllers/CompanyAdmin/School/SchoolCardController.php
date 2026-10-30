<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class SchoolCardController extends Controller
{
    private function cid(): int
    {
        return (int) auth()->user()->company_id;
    }

    private function years(int $companyId)
    {
        return DB::table('school_years')
            ->where('company_id', $companyId)
            ->orderByDesc('starts_on')
            ->get();
    }

    private function pupils(int $companyId, int $yearId, ?int $classId = null, string $search = '')
    {
        $query = DB::table('school_enrollments as e')
            ->join('school_students as s', function ($join) {
                $join->on('s.id', '=', 'e.school_student_id')
                    ->on('s.company_id', '=', 'e.company_id');
            })
            ->join('school_classes as c', function ($join) {
                $join->on('c.id', '=', 'e.school_class_id')
                    ->on('c.company_id', '=', 'e.company_id');
            })
            ->where('e.company_id', $companyId)
            ->where('e.school_year_id', $yearId)
            ->where('c.school_year_id', $yearId)
            ->where('e.status', 'enrolled');

        if ($classId !== null) {
            $query->where('e.school_class_id', $classId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('s.last_name', 'like', '%' . $search . '%')
                    ->orWhere('s.first_name', 'like', '%' . $search . '%')
                    ->orWhere('s.student_number', 'like', '%' . $search . '%');
            });
        }

        $columns = [
            's.id', 's.student_number', 's.first_name', 's.last_name',
            'e.school_class_id', 'c.name as class_name',
        ];

        foreach (['date_of_birth', 'place_of_birth', 'gender'] as $field) {
            $columns[] = Schema::hasColumn('school_students', $field)
                ? 's.' . $field
                : DB::raw('NULL as ' . $field);
        }

        $photoField = collect(['photo', 'photo_path', 'picture', 'image'])
            ->first(fn ($field) => Schema::hasColumn('school_students', $field));
        $columns[] = $photoField
            ? 's.' . $photoField . ' as card_photo'
            : DB::raw('NULL as card_photo');

        return $query->select($columns)
            ->distinct()
            ->orderBy('s.last_name')
            ->orderBy('s.first_name');
    }

    private function context(Request $request): array
    {
        $cid = $this->cid();
        $years = $this->years($cid);
        $default = $years->firstWhere('is_active', 1) ?? $years->first();

        $yearId = $request->filled('year_id')
            ? $request->integer('year_id')
            : (int) ($default?->id ?? 0);

        $year = $yearId
            ? DB::table('school_years')->where('company_id', $cid)->where('id', $yearId)->firstOrFail()
            : null;

        $classes = $year
            ? DB::table('school_classes')
                ->where('company_id', $cid)
                ->where('school_year_id', $yearId)
                ->orderBy('name')->get()
            : collect();

        $classId = $request->filled('class_id') ? $request->integer('class_id') : null;
        if ($classId !== null) {
            abort_unless($classes->contains('id', $classId), 404);
        }

        return compact('cid', 'years', 'year', 'classes', 'classId');
    }

    public function index(Request $request)
    {
        $ctx = $this->context($request);
        $search = trim((string) $request->query('q', ''));

        $students = $ctx['year']
            ? $this->pupils($ctx['cid'], (int) $ctx['year']->id, $ctx['classId'], $search)
                ->paginate(20)->withQueryString()
            : null;

        return view('company.school.cards.index', array_merge($ctx, compact('students', 'search')));
    }

    public function print(Request $request)
    {
        $ctx = $this->context($request);
        abort_unless($ctx['year'], 404, 'Aucune année scolaire disponible.');

        $studentId = $request->filled('student_id') ? $request->integer('student_id') : null;
        if (!$studentId) {
            abort_unless($ctx['classId'], 422, 'Sélectionnez une classe pour l’impression groupée.');
        }

        $query = $this->pupils($ctx['cid'], (int) $ctx['year']->id, $ctx['classId']);
        if ($studentId) {
            $query->where('s.id', $studentId);
        }

        $students = $query->limit(201)->get();
        abort_if($students->isEmpty(), 404, 'Aucun élève trouvé.');
        abort_if($students->count() > 200, 422, 'Limite de 200 cartes par impression.');

        $company = DB::table('companies')->where('id', $ctx['cid'])->firstOrFail();
        $settings = DB::table('school_settings')->where('company_id', $ctx['cid'])->first();

        $schoolName = $settings?->school_name ?: $company->name;
        $schoolLogo = $settings?->logo ?: ($company->logo ?? null);
        $schoolStamp = $settings?->stamp ?? null;
        $principalName = $settings?->principal_name ?: ($settings?->director_name ?? null);
        $motto = $settings?->motto ?? null;
        $year = $ctx['year'];

        $cards = $students->map(function ($student) use ($ctx, $year) {
            $student->verification_url = URL::temporarySignedRoute(
                'school.cards.verify',
                now()->addYears(2),
                [
                    'companyId' => $ctx['cid'],
                    'yearId' => $year->id,
                    'studentId' => $student->id,
                ]
            );
            return $student;
        });

        $data = compact('cards', 'company', 'year', 'schoolName', 'schoolLogo', 'schoolStamp', 'principalName', 'motto');

        if ($request->boolean('pdf')) {
            abort_unless(class_exists(\Barryvdh\DomPDF\Facade\Pdf::class), 501,
                'Installer barryvdh/laravel-dompdf pour exporter en PDF.');

            return \Barryvdh\DomPDF\Facade\Pdf::loadView('company.school.cards.print', $data)
                ->setPaper('a4', 'portrait')
                ->download('cartes-scolaires-' . $year->id . '.pdf');
        }

        return view('company.school.cards.print', $data);
    }

    public function verify(Request $request, int $companyId, int $yearId, int $studentId)
    {
        abort_unless($request->hasValidSignature(), 403, 'Lien de vérification invalide ou expiré.');

        $student = DB::table('school_enrollments as e')
            ->join('school_students as s', function ($join) {
                $join->on('s.id', '=', 'e.school_student_id')
                    ->on('s.company_id', '=', 'e.company_id');
            })
            ->join('school_classes as c', function ($join) {
                $join->on('c.id', '=', 'e.school_class_id')
                    ->on('c.company_id', '=', 'e.company_id');
            })
            ->where('e.company_id', $companyId)
            ->where('e.school_year_id', $yearId)
            ->where('c.school_year_id', $yearId)
            ->where('e.school_student_id', $studentId)
            ->where('e.status', 'enrolled')
            ->select('s.student_number', 's.first_name', 's.last_name')
            ->first();

        abort_unless($student, 404, 'Carte scolaire introuvable ou inscription inactive.');
        return response()->view('company.school.cards.verify', compact('student'));
    }
}

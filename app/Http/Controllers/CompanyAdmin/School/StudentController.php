<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\{SchoolClass, SchoolLevel, SchoolSection, SchoolStudent, SchoolYear};
use App\Services\SchoolStudentNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Storage, Validator};
use Illuminate\Validation\Rules\File;

class StudentController extends Controller
{
    private function cid(Request $r): int { return (int) $r->user()->company_id; }

    private function authorizeStudent(Request $r, SchoolStudent $student): void
    {
        abort_unless((int) $student->company_id === $this->cid($r), 404);
    }

    private function filtered(Request $r)
    {
        $cid = $this->cid($r);
        $query = SchoolStudent::query()->where('school_students.company_id', $cid);
        if ($r->filled('q')) {
            $q = trim((string) $r->query('q'));
            $query->where(fn ($b) => $b->where('last_name', 'like', "%{$q}%")
                ->orWhere('first_name', 'like', "%{$q}%")
                ->orWhere('student_number', 'like', "%{$q}%"));
        }
        if ($r->filled('year_id') || $r->filled('section_id') || $r->filled('level_id') || $r->filled('class_id')) {
            $query->whereHas('enrollments', function ($e) use ($r, $cid) {
                $e->where('company_id', $cid);
                if ($r->filled('year_id')) $e->where('school_year_id', $r->integer('year_id'));
                if ($r->filled('class_id')) $e->where('school_class_id', $r->integer('class_id'));
                if ($r->filled('section_id') || $r->filled('level_id')) {
                    $e->whereHas('schoolClass', function ($c) use ($r, $cid) {
                        $c->where('company_id', $cid);
                        if ($r->filled('level_id')) $c->where('school_level_id', $r->integer('level_id'));
                        if ($r->filled('section_id')) $c->whereHas('level', fn ($l) => $l->where('company_id', $cid)->where('school_section_id', $r->integer('section_id')));
                    });
                }
            });
        }
        return match ($r->query('sort', 'name')) {
            'matricule' => $query->orderBy('student_number'),
            'recent' => $query->orderByDesc('created_at')->orderByDesc('id'),
            default => $query->orderBy('last_name')->orderBy('first_name')->orderBy('id'),
        };
    }

    public function index(Request $r)
    {
        $cid = $this->cid($r);
        $students = $this->filtered($r)->with(['enrollments' => function ($q) use ($cid) {
            $q->where('company_id', $cid)->with(['year', 'schoolClass.level.section'])
                ->orderByDesc('enrolled_on')->orderByDesc('id');
        }])->paginate(20)->withQueryString();
        $years = SchoolYear::where('company_id', $cid)->orderByDesc('starts_on')->get();
        $sections = SchoolSection::where('company_id', $cid)->orderBy('name')->get();
        $levels = SchoolLevel::where('company_id', $cid)->orderBy('sort_order')->get();
        $classes = SchoolClass::where('company_id', $cid)->orderBy('name')->get();
        return view('company.school.students.index', compact('students', 'years', 'sections', 'levels', 'classes'));
    }

    public function create()
    {
        return view('company.school.students.form', ['student' => new SchoolStudent()]);
    }

    private function rules(): array
    {
        return [
            'last_name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before_or_equal:today',
            'place_of_birth' => 'nullable|string|max:255',
            'gender' => 'nullable|in:M,F',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:40',
            'guardian_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'photo' => ['nullable', File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max('2mb')],
        ];
    }

    private function studentData(array $validated): array
    {
        unset($validated['photo']);
        return $validated;
    }

    public function store(Request $r, SchoolStudentNumberService $numbers)
    {
        $validated = $r->validate($this->rules());
        $data = $this->studentData($validated);
        $photoPath = null;
        try {
            if ($r->hasFile('photo')) {
                $photoPath = $r->file('photo')->store('school/students/' . $this->cid($r), 'public');
                $data['photo'] = $photoPath;
            }
            // Le service existant conserve la génération automatique du matricule.
            $numbers->create($this->cid($r), $data);
        } catch (\Throwable $e) {
            if ($photoPath) Storage::disk('public')->delete($photoPath);
            throw $e;
        }
        return redirect()->route('company.school.students.index')->with('success', 'Élève enregistré avec matricule automatique.');
    }

    public function edit(Request $r, SchoolStudent $student)
    {
        $this->authorizeStudent($r, $student);
        return view('company.school.students.form', compact('student'));
    }

    public function update(Request $r, SchoolStudent $student)
    {
        $this->authorizeStudent($r, $student);
        $validated = $r->validate($this->rules() + ['remove_photo' => 'sometimes|boolean']);
        $data = $this->studentData($validated);
        unset($data['remove_photo']);
        $oldPhoto = $student->photo;
        $newPhoto = null;
        try {
            if ($r->hasFile('photo')) {
                $newPhoto = $r->file('photo')->store('school/students/' . $this->cid($r), 'public');
                $data['photo'] = $newPhoto;
            } elseif ($r->boolean('remove_photo')) {
                $data['photo'] = null;
            }
            $student->update($data);
        } catch (\Throwable $e) {
            if ($newPhoto) Storage::disk('public')->delete($newPhoto);
            throw $e;
        }
        if ($oldPhoto && array_key_exists('photo', $data) && $oldPhoto !== $data['photo']) {
            Storage::disk('public')->delete($oldPhoto);
        }
        return redirect()->route('company.school.students.index')->with('success', 'Élève modifié.');
    }

    public function destroy(Request $r, SchoolStudent $student)
    {
        $this->authorizeStudent($r, $student);
        if ($student->enrollments()->exists()) return back()->withErrors(['student' => 'Impossible de supprimer un élève déjà inscrit.']);
        $photo = $student->photo;
        $student->delete();
        if ($photo) Storage::disk('public')->delete($photo);
        return back()->with('success', 'Élève supprimé.');
    }

    public function export(Request $r)
    {
        $students = $this->filtered($r)->get();
        $headers = ['Matricule','Nom','Prénom','Date de naissance','Lieu de naissance','Sexe','Tuteur','Téléphone tuteur','Email tuteur','Adresse'];
        return \Maatwebsite\Excel\Facades\Excel::download(
            new class($students, $headers) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function __construct(private $students, private array $headers) {}
                public function headings(): array { return $this->headers; }
                public function collection() { return $this->students->map(fn ($s) => [
                    $s->student_number, $s->last_name, $s->first_name, $s->date_of_birth?->format('Y-m-d'),
                    $s->place_of_birth, $s->gender, $s->guardian_name, $s->guardian_phone, $s->guardian_email, $s->address,
                ]); }
            }, 'eleves_' . date('Ymd_His') . '.xlsx'
        );
    }

    public function template()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function headings(): array { return ['nom','prenom','date_naissance','lieu_naissance','sexe','tuteur','telephone_tuteur','email_tuteur','adresse']; }
                public function array(): array { return [['Mballa','Marie','2012-05-15','Douala','F','Mballa Jean','699000000','','Douala']]; }
            }, 'modele_import_eleves.xlsx'
        );
    }

    public function import(Request $r, SchoolStudentNumberService $numbers)
    {
        $r->validate(['file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120']);
        $sheets = \Maatwebsite\Excel\Facades\Excel::toCollection(null, $r->file('file'));
        $rows = $sheets->first();
        if (!$rows || $rows->count() < 2) return back()->withErrors(['file' => 'Le fichier est vide.']);
        $expected = ['nom','prenom','date_naissance','lieu_naissance','sexe','tuteur','telephone_tuteur','email_tuteur','adresse'];
        $actual = $rows->first()->map(fn ($v) => mb_strtolower(trim((string) $v)))->values()->all();
        if (array_slice($actual, 0, count($expected)) !== $expected) return back()->withErrors(['file' => 'Colonnes invalides. Téléchargez le modèle Excel et conservez ses en-têtes.']);
        $created = 0;
        $errors = [];
        foreach ($rows->skip(1) as $i => $row) {
            $values = $row->values()->all();
            if (!array_filter($values, fn ($v) => trim((string) $v) !== '')) continue;
            $data = [
                'last_name' => $values[0] ?? null, 'first_name' => $values[1] ?? null,
                'date_of_birth' => $values[2] ?? null, 'place_of_birth' => $values[3] ?? null,
                'gender' => isset($values[4]) ? strtoupper(trim((string) $values[4])) : null,
                'guardian_name' => $values[5] ?? null, 'guardian_phone' => $values[6] ?? null,
                'guardian_email' => $values[7] ?? null, 'address' => $values[8] ?? null,
            ];
            if (is_numeric($data['date_of_birth']) && $data['date_of_birth'] !== null) {
                $data['date_of_birth'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $data['date_of_birth'])->format('Y-m-d');
            }
            foreach ($data as $key => $value) if ($value === '') $data[$key] = null;
            $validator = Validator::make($data, $this->studentDataRules());
            if ($validator->fails()) { $errors[] = 'Ligne ' . ($i + 1) . ' : ' . $validator->errors()->first(); continue; }
            $numbers->create($this->cid($r), $validator->validated());
            $created++;
        }
        $message = "$created élève(s) importé(s).";
        if ($errors) return back()->with('success', $message)->withErrors(['file' => implode(' | ', array_slice($errors, 0, 8))]);
        return back()->with('success', $message);
    }

    private function studentDataRules(): array
    {
        $rules = $this->rules();
        unset($rules['photo']);
        return $rules;
    }
}

<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SchoolTimetableController extends Controller
{
    private function cid(): int { return (int) auth()->user()->company_id; }

    private function context(Request $request): array
    {
        $cid = $this->cid();
        $years = DB::table('school_years')->where('company_id', $cid)->orderByDesc('id')->get();
        $yearId = (int) $request->integer('year_id', (int) ($years->firstWhere('is_active', 1)?->id ?? $years->first()?->id ?? 0));
        abort_unless($years->contains('id', $yearId), 404);
        $classes = DB::table('school_classes')->where('company_id', $cid)->orderBy('name')->get();
        $classId = (int) $request->integer('class_id', (int) ($classes->first()?->id ?? 0));
        abort_unless($classes->contains('id', $classId), 404);
        return compact('cid', 'years', 'yearId', 'classes', 'classId');
    }

    public function index(Request $request)
    {
        $cid = $this->cid();
        $years = DB::table('school_years')->where('company_id', $cid)->orderByDesc('id')->get();
        $classes = DB::table('school_classes')->where('company_id', $cid)->orderBy('name')->get();
        $yearId = (int) $request->integer('year_id', (int) ($years->firstWhere('is_active', 1)?->id ?? $years->first()?->id ?? 0));
        $classId = (int) $request->integer('class_id', (int) ($classes->first()?->id ?? 0));
        $valid = $years->contains('id', $yearId) && $classes->contains('id', $classId);
        $rooms = DB::table('school_timetable_rooms')->where('company_id', $cid)->orderBy('name')->get();
        $subjects = collect(); $slots = collect();
        if ($valid) {
            $subjects = DB::table('school_class_subjects as cs')
                ->join('school_subjects as s', 's.id', '=', 'cs.school_subject_id')
                ->where('cs.company_id', $cid)->where('s.company_id', $cid)
                ->where('cs.school_class_id', $classId)
                ->select('cs.id', 's.name')->orderBy('s.name')->get();
            $slots = DB::table('school_timetable_slots as t')
                ->join('school_class_subjects as cs', 'cs.id', '=', 't.school_class_subject_id')
                ->join('school_subjects as s', 's.id', '=', 'cs.school_subject_id')
                ->leftJoin('school_timetable_rooms as r', function ($join) use ($cid) {
                    $join->on('r.id', '=', 't.room_id')->where('r.company_id', '=', $cid);
                })
                ->where('t.company_id', $cid)->where('t.school_year_id', $yearId)
                ->where('t.school_class_id', $classId)->where('cs.company_id', $cid)
                ->where('s.company_id', $cid)
                ->select('t.*', 's.name as subject_name', 'r.name as room_name')
                ->orderBy('t.weekday')->orderBy('t.starts_at')->get();
        }
        return view('company.school.timetables.index', compact('years', 'classes', 'yearId', 'classId', 'rooms', 'subjects', 'slots', 'valid'));
    }

    public function storeRoom(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'capacity' => ['nullable', 'integer', 'min:1', 'max:10000']]);
        $cid = $this->cid();
        if (DB::table('school_timetable_rooms')->where('company_id', $cid)->where('name', $data['name'])->exists()) {
            throw ValidationException::withMessages(['name' => 'Cette salle existe déjà.']);
        }
        DB::table('school_timetable_rooms')->insert(['company_id' => $cid, 'name' => $data['name'], 'capacity' => $data['capacity'] ?? null, 'created_at' => now(), 'updated_at' => now()]);
        return back()->with('success', 'Salle ajoutée.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'year_id' => ['required', 'integer'], 'class_id' => ['required', 'integer'],
            'subject_id' => ['required', 'integer'], 'weekday' => ['required', 'integer', 'between:1,6'],
            'starts_at' => ['required', 'date_format:H:i'], 'ends_at' => ['required', 'date_format:H:i', 'after:starts_at'],
            'room_id' => ['nullable', 'integer'], 'teacher_id' => ['nullable', 'integer'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);
        $cid = $this->cid();
        abort_unless(DB::table('school_years')->where('company_id', $cid)->where('id', $data['year_id'])->exists(), 404);
        abort_unless(DB::table('school_classes')->where('company_id', $cid)->where('id', $data['class_id'])->exists(), 404);
        abort_unless(DB::table('school_class_subjects')->where('company_id', $cid)->where('school_class_id', $data['class_id'])->where('id', $data['subject_id'])->exists(), 404);
        if (!empty($data['room_id'])) abort_unless(DB::table('school_timetable_rooms')->where('company_id', $cid)->where('id', $data['room_id'])->exists(), 404);
        // Le schéma exact des enseignants dépend de votre module : aucune validation d'affectation n'est présumée ici.
        DB::transaction(function () use ($cid, $data) {
            // Verrouille les écritures de la même entreprise pour éviter deux créations concurrentes.
            DB::table('companies')->where('id', $cid)->lockForUpdate()->first();
            $overlap = DB::table('school_timetable_slots')
                ->where('company_id', $cid)->where('school_year_id', $data['year_id'])
                ->where('weekday', $data['weekday'])
                ->where('starts_at', '<', $data['ends_at'])->where('ends_at', '>', $data['starts_at']);
            $classBusy = (clone $overlap)->where('school_class_id', $data['class_id'])->exists();
            $roomBusy = !empty($data['room_id']) && (clone $overlap)->where('room_id', $data['room_id'])->exists();
            $teacherBusy = !empty($data['teacher_id']) && (clone $overlap)->where('teacher_id', $data['teacher_id'])->exists();
            if ($classBusy || $roomBusy || $teacherBusy) {
                throw ValidationException::withMessages(['starts_at' => 'Conflit d’horaire : classe, salle ou enseignant déjà occupé sur cette plage.']);
            }
            DB::table('school_timetable_slots')->insert([
                'company_id' => $cid, 'school_year_id' => $data['year_id'],
                'school_class_id' => $data['class_id'], 'school_class_subject_id' => $data['subject_id'],
                'weekday' => $data['weekday'], 'starts_at' => $data['starts_at'], 'ends_at' => $data['ends_at'],
                'room_id' => $data['room_id'] ?? null, 'teacher_id' => $data['teacher_id'] ?? null,
                'note' => $data['note'] ?? null, 'created_at' => now(), 'updated_at' => now(),
            ]);
        });
        return redirect()->route('company.school.timetables.index', ['year_id' => $data['year_id'], 'class_id' => $data['class_id']])->with('success', 'Créneau ajouté.');
    }

    public function destroy(int $slot)
    {
        $deleted = DB::table('school_timetable_slots')->where('company_id', $this->cid())->where('id', $slot)->delete();
        abort_unless($deleted, 404);
        return back()->with('success', 'Créneau supprimé.');
    }

    public function print(Request $request)
    {
        $response = $this->index($request);
        return view('company.school.timetables.print', $response->getData());
    }
}

<?php

namespace App\Http\Controllers\CompanyAdmin\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolTeacher;
use App\Models\SchoolTeacherAssignment;
use Illuminate\Support\Facades\DB;

class TeacherSpaceController extends Controller
{
    private function teacher(): SchoolTeacher
    {
        $user = auth()->user();

        abort_unless(
            $user
                && $user->role === 'school_teacher'
                && $user->company_id,
            403,
            'Accès réservé aux enseignants.'
        );

        return SchoolTeacher::query()
            ->where('company_id', $user->company_id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->firstOrFail();
    }

    public function dashboard()
    {
        $teacher = $this->teacher();

        $assignments = SchoolTeacherAssignment::query()
            ->where('company_id', $teacher->company_id)
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->get();

        $classIds = $assignments
            ->pluck('class_id')
            ->unique()
            ->values();

        $subjectIds = $assignments
            ->pluck('subject_id')
            ->unique()
            ->values();

        $classes = DB::table('school_classes')
            ->where('company_id', $teacher->company_id)
            ->whereIn('id', $classIds)
            ->get();

        $subjects = DB::table('school_subjects')
            ->where('company_id', $teacher->company_id)
            ->whereIn('id', $subjectIds)
            ->get();

        return view('company.school.teacher-space.dashboard', [
            'teacher' => $teacher,
            'assignments' => $assignments,
            'classes' => $classes,
            'subjects' => $subjects,
        ]);
    }
}

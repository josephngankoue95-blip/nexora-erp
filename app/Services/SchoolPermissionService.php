<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class SchoolPermissionService
{
    public const LABELS = [
        'school.dashboard.view' => 'Tableau de bord',
        'school.students.view' => 'Consulter les élèves',
        'school.students.manage' => 'Gérer les élèves',
        'school.enrollments.manage' => 'Gérer les inscriptions',
        'school.classes.manage' => 'Gérer les classes',
        'school.teachers.manage' => 'Gérer les enseignants',
        'school.grades.view' => 'Consulter les notes',
        'school.grades.manage' => 'Saisir les notes',
        'school.reports.view' => 'Consulter les bulletins',
        'school.timetables.view' => 'Consulter les emplois du temps',
        'school.attendance.manage' => 'Gérer les absences',
        'school.fees.manage' => 'Gérer les tarifs',
        'school.tuition.manage' => 'Gérer les paiements scolaires',
        'school.users.manage' => 'Gérer les utilisateurs',
        'school.access.manage' => 'Gérer les accès',
        'school.permissions.manage' => 'Gérer les permissions',
        'school.settings.manage' => 'Paramètres scolaires',
    ];

    public const DEFAULTS = [
        'school_admin' => ['*'],
        'school_principal' => ['school.dashboard.view', 'school.students.view', 'school.classes.manage', 'school.teachers.manage', 'school.grades.view', 'school.reports.view', 'school.timetables.view', 'school.attendance.manage'],
        'school_director' => ['school.dashboard.view', 'school.students.view', 'school.classes.manage', 'school.teachers.manage', 'school.grades.view', 'school.reports.view', 'school.timetables.view'],
        'school_teacher' => ['school.dashboard.view', 'school.students.view', 'school.grades.view', 'school.grades.manage', 'school.reports.view', 'school.timetables.view'],
        'school_bursar' => ['school.dashboard.view', 'school.fees.manage', 'school.tuition.manage'],
        'school_supervisor' => ['school.dashboard.view', 'school.students.view', 'school.attendance.manage', 'school.timetables.view'],
        'school_sector_supervisor' => ['school.dashboard.view', 'school.students.view', 'school.attendance.manage'],
        'school_student' => ['school.dashboard.view', 'school.reports.view', 'school.timetables.view'],
        'school_parent' => ['school.dashboard.view', 'school.reports.view'],
        'school_librarian' => ['school.dashboard.view'],
    ];

    public function allowed(User $user, string $permission): bool
    {
        if (!array_key_exists($permission, self::LABELS)) {
            return false;
        }

        if (!$user->is_active || !$user->company_id) {
            return false;
        }

        $company = $user->company;
        if (!$company || !$company->is_active) {
            return false;
        }

        if ($user->role === 'companyadmin') {
            return true;
        }

        // Non délégable, même par une surcharge individuelle.
        if ($permission === 'school.permissions.manage') {
            return false;
        }

        if (!array_key_exists($user->role, self::DEFAULTS)) {
            return false;
        }

        $override = DB::table('school_user_permission_overrides')
            ->where('company_id', $user->company_id)
            ->where('user_id', $user->id)
            ->where('permission', $permission)
            ->value('allowed');

        if ($override !== null) {
            return (bool) $override;
        }

        $defaults = self::DEFAULTS[$user->role];
        return in_array('*', $defaults, true)
            || in_array($permission, $defaults, true);
    }
}

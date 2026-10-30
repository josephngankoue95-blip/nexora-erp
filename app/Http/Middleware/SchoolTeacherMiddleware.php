<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SchoolTeacherMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        abort_unless($user->role === 'school_teacher', 403, 'Accès réservé aux enseignants.');

        if (!$user->company_id || !$user->company || !$user->company->is_active || !$user->is_active) {
            abort(403, 'Compte ou entreprise inactif ou non associé.');
        }

        $teacherExists = DB::table('school_teachers')
            ->where('company_id', $user->company_id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();

        abort_unless($teacherExists, 403, 'Aucun profil enseignant actif associé à ce compte.');

        return $next($request);
    }
}

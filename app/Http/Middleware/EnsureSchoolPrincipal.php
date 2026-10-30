<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolPrincipal
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) return redirect()->route('login');
        abort_unless($user->role === 'school_principal', 403, 'Accès réservé au proviseur.');
        abort_unless($user->is_active && $user->company_id && $user->company && $user->company->is_active, 403, 'Compte ou établissement inactif.');
        return $next($request);
    }
}

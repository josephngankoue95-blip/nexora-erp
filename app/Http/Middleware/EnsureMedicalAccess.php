<?php

namespace App\Http\Middleware;

use App\Services\CompanyModuleService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMedicalAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user && $user->company, 403);

        $service = app(CompanyModuleService::class);

        $canAccessClinic =
            $service->isEnabled($user->company, 'clinic')
            && $user->hasPermission('clinic.view');

        $canAccessHospital =
            $service->isEnabled($user->company, 'hospital')
            && $user->hasPermission('hospital.view');

        abort_unless(
            $canAccessClinic || $canAccessHospital,
            403,
            'Accès au module médical non autorisé.'
        );

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use App\Services\SchoolPermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolPermission
{
    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {

        $user = $request->user();

        abort_unless(
            $user &&
            app(SchoolPermissionService::class)->allowed(
                $user,
                $permission
            ),
            403,
            'Vous ne disposez pas des permissions nécessaires.'
        );

        return $next($request);
    }
}

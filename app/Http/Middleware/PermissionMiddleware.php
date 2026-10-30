<?php

namespace App\Http\Middleware;

use App\Services\RolePermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function __construct(
        protected RolePermissionService $permissions
    ) {}

    public function handle(
        Request $request,
        Closure $next,
        string $module
    ): Response {

        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$this->permissions->can($user, $module)) {

            abort(403, 'Vous n’avez pas l’autorisation d’accéder à ce module.');
        }

        return $next($request);
    }
}
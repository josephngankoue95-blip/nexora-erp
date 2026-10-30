<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\CompanyAdminMiddleware;
use App\Http\Middleware\SchoolTeacherMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\EnsureModuleEnabled;
use App\Http\Middleware\EnsureModulePermission;
use App\Http\Middleware\EnsureSchoolPermission;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([

            // SUPER ADMIN
            'superadmin' => SuperAdminMiddleware::class,

            // ENTREPRISE
            'companyadmin' => CompanyAdminMiddleware::class,

            // MODULES ERP
            'module' => EnsureModuleEnabled::class,
            'module_permission' => EnsureModulePermission::class,

            // GESTION SCOLAIRE
            'school.teacher' => SchoolTeacherMiddleware::class,
            'school_permission' => EnsureSchoolPermission::class,

            // RÔLES ET PERMISSIONS GÉNÉRALES
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,

            //ACCES MEDICAL
            'medical_access' => \App\Http\Middleware\EnsureMedicalAccess::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
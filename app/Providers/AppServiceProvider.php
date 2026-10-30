<?php

namespace App\Providers;

use App\Services\RolePermissionService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Services\CompanyModuleService;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Directive Blade : @canPermission()
        |--------------------------------------------------------------------------
        |
        | Exemple :
        |
        | @canPermission('products.create')
        |     <a href="...">Ajouter</a>
        | @endcanPermission
        |
        */

        Blade::if('canPermission', function (string $permission): bool {

            $user = auth()->user();

            if (!$user) {
                return false;
            }

            return app(RolePermissionService::class)
                ->can($user, $permission);
        });

        View::composer('layouts.company', function ($view) {

        $company = auth()->user()?->company;

        $moduleService = app(
            CompanyModuleService::class
        );

        $activeModules = $moduleService
            ->getActiveModules($company);

        $activeModuleSlugs = $moduleService
            ->getActiveModuleSlugs($company);

        $view->with([
            'activeModules' => $activeModules,
            'activeModuleSlugs' => $activeModuleSlugs,
        ]);
    });
    
    }
}
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Storefront\StorefrontController;
use App\Http\Controllers\Storefront\StorefrontProductController;
use App\Http\Controllers\Storefront\StorefrontCartController;
use App\Http\Controllers\Storefront\StorefrontReviewController;
use App\Http\Controllers\Storefront\StorefrontCheckoutController;



/*
|--------------------------------------------------------------------------
| SUPER ADMIN
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\CompanyController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\SuperAdmin\SubscriptionController;
use App\Http\Controllers\SuperAdmin\SubscriptionPlanController;
use App\Http\Controllers\SuperAdmin\PaymentController;
use App\Http\Controllers\SuperAdmin\PlatformSettingController;
use App\Http\Controllers\SuperAdmin\ModuleController;


/*
|--------------------------------------------------------------------------
| COMPANY
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\CompanyAdmin\DashboardController as CompanyDashboardController;
use App\Http\Controllers\CompanyAdmin\CategoryController;
use App\Http\Controllers\CompanyAdmin\ProductController;
use App\Http\Controllers\CompanyAdmin\StockController;
use App\Http\Controllers\CompanyAdmin\ClientController;
use App\Http\Controllers\CompanyAdmin\SupplierController;
use App\Http\Controllers\CompanyAdmin\SaleController;
use App\Http\Controllers\CompanyAdmin\SalePaymentController;
use App\Http\Controllers\CompanyAdmin\PaymentSettingController;
use App\Http\Controllers\CompanyAdmin\FinanceController;
use App\Http\Controllers\CompanyAdmin\ReportController;
use App\Http\Controllers\CompanyAdmin\ReportExportController;
use App\Http\Controllers\CompanyAdmin\PurchaseController;
use App\Http\Controllers\CompanyAdmin\PurchasePaymentController;
use App\Http\Controllers\CompanyAdmin\UserController;
use App\Http\Controllers\CompanyAdmin\UserPermissionController;
use App\Http\Controllers\CompanyAdmin\SettingController;
use App\Http\Controllers\CompanyAdmin\ServiceController;
use App\Http\Controllers\CompanyAdmin\EcommerceController;
use App\Http\Controllers\CompanyAdmin\ModuleLauncherController;
use App\Http\Controllers\CompanyAdmin\WebsiteController;
use App\Http\Controllers\CompanyAdmin\EcommercePromotionController;
use App\Http\Controllers\CompanyAdmin\EcommerceReviewController;
use App\Http\Controllers\CompanyAdmin\EcommerceStoreSettingController;
use App\Http\Controllers\CompanyAdmin\EcommerceReportController;
use App\Http\Controllers\CompanyAdmin\EcommerceReportExportController;
use App\Http\Controllers\CompanyAdmin\EcommerceCategoryController;
use App\Http\Controllers\CompanyAdmin\EcommerceCollectionController;
use App\Http\Controllers\CompanyAdmin\WebsitePageController;
use App\Http\Controllers\CompanyAdmin\WebsiteMediaController;
use App\Http\Controllers\CompanyAdmin\WebsiteMediaPickerController;
use App\Http\Controllers\CompanyAdmin\WebsiteMenuController;
use App\Http\Controllers\CompanyAdmin\WebsiteSettingController;
use App\Http\Controllers\CompanyAdmin\WebsiteTemplateController;
use App\Http\Controllers\CompanyAdmin\WebsiteBuilderController;


use App\Http\Controllers\CompanyAdmin\School\StudentController;
use App\Http\Controllers\CompanyAdmin\School\EnrollmentController;
use App\Http\Controllers\CompanyAdmin\School\ClassSubjectController;
use App\Http\Controllers\CompanyAdmin\School\SchoolCardController;
use App\Http\Controllers\CompanyAdmin\School\SchoolHonorController;
use App\Http\Controllers\CompanyAdmin\School\SchoolUserController;
use App\Http\Controllers\CompanyAdmin\School\SchoolAccessController;
use App\Http\Controllers\CompanyAdmin\School\SchoolPermissionController;
use App\Http\Controllers\CompanyAdmin\School\SchoolAttendanceController;
use App\Http\Controllers\CompanyAdmin\School\SchoolAttendanceJustificationController;
use App\Http\Controllers\CompanyAdmin\School\SchoolAttendanceReportController;
use App\Http\Controllers\CompanyAdmin\School\SchoolPrincipalDashboardController;
use App\Http\Controllers\CompanyAdmin\School\SchoolPrincipalAcademicController;
use App\Http\Controllers\CompanyAdmin\School\SchoolPrincipalTeacherController;
use App\Http\Controllers\CompanyAdmin\School\SchoolPrincipalResultsController;
use App\Http\Controllers\CompanyAdmin\School\SchoolPrincipalRankingController;
use App\Http\Controllers\CompanyAdmin\School\SchoolPrincipalBulletinController;
use App\Http\Controllers\CompanyAdmin\School\SchoolPrincipalTuitionController;

use App\Http\Controllers\CompanyAdmin\Medical\ClinicDashboardController;
use App\Http\Controllers\CompanyAdmin\Medical\HospitalDashboardController;
use App\Http\Controllers\CompanyAdmin\Medical\PatientController;
use App\Http\Controllers\CompanyAdmin\Medical\AppointmentController;
use App\Http\Controllers\CompanyAdmin\Medical\PractitionerController;
use App\Http\Controllers\CompanyAdmin\Medical\ConsultationController;


use App\Http\Controllers\Website\WebsitePublicController;



use App\Http\Controllers\PaymentWebhookController;


/*
|--------------------------------------------------------------------------
| PAGE D'ACCUEIL
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();

    if (!$user->is_active) {
        auth()->logout();
        return redirect()->route('login')->withErrors([
            'email' => 'Votre compte est désactivé.',
        ]);
    }

    if ($user->isSuperAdmin()) {
        return redirect()->route('superadmin.dashboard');
    }

    if (!$user->company_id || !$user->company || !$user->company->is_active) {
        auth()->logout();
        return redirect()->route('login')->withErrors([
            'email' => 'Votre entreprise est introuvable ou inactive.',
        ]);
    }

    $companyRoles = [
        'companyadmin', 'director', 'manager', 'seller',
        'cashier', 'stock_manager', 'accountant', 'employee',
    ];

    if (in_array($user->role, $companyRoles, true)) {
        return redirect()->route('company.modules.index');
    }

    // Garder les mêmes noms de routes que dans AuthenticatedSessionController.
    $schoolDashboards = [
        'school_admin' => 'company.school.dashboard',
        'school_principal' => 'company.school.principal.dashboard',
        'school_director' => 'company.school.director.dashboard',
        'school_teacher' => 'company.school.teacher-space.dashboard',
        'school_supervisor' => 'company.school.supervisor.dashboard',
        'school_sector_supervisor' => 'company.school.sector-supervisor.dashboard',
        'school_bursar' => 'company.school.bursar.dashboard',
        'school_librarian' => 'company.school.librarian.dashboard',
        'school_student' => 'company.school.student.dashboard',
        'school_parent' => 'company.school.parent.dashboard',
    ];

    $destination = $schoolDashboards[$user->role] ?? null;

    if ($destination !== null && Route::has($destination)) {
        return redirect()->route($destination);
    }

    auth()->logout();
    return redirect()->route('login')->withErrors([
        'email' => $destination === null
            ? 'Votre compte ne possède aucun rôle valide.'
            : 'Votre espace scolaire n’est pas encore disponible.',
    ]);
});


/*
|--------------------------------------------------------------------------
| ESPACE SUPER ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'superadmin',
])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {


        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [
                DashboardController::class,
                'index',
            ]
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | ENTREPRISES
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'companies',
            CompanyController::class
        );


        /*
        |--------------------------------------------------------------------------
        | ACTIVER / DÉSACTIVER UNE ENTREPRISE
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/companies/{company}/toggle-status',
            [
                CompanyController::class,
                'toggleStatus',
            ]
        )->name('companies.toggle-status');


        /*
        |--------------------------------------------------------------------------
        | MODULES D'UNE ENTREPRISE
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/companies/{company}/modules',
            [
                ModuleController::class,
                'edit',
            ]
        )->name('companies.modules.edit');


        Route::put(
            '/companies/{company}/modules',
            [
                ModuleController::class,
                'update',
            ]
        )->name('companies.modules.update');


        /*
        |--------------------------------------------------------------------------
        | UTILISATEURS SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/users',
            [
                SuperAdminUserController::class,
                'index',
            ]
        )->name('users.index');


        Route::get(
            '/users/create',
            [
                SuperAdminUserController::class,
                'create',
            ]
        )->name('users.create');


        Route::post(
            '/users',
            [
                SuperAdminUserController::class,
                'store',
            ]
        )->name('users.store');


        Route::get(
            '/users/{user}',
            [
                SuperAdminUserController::class,
                'show',
            ]
        )->name('users.show');


        Route::get(
            '/users/{user}/edit',
            [
                SuperAdminUserController::class,
                'edit',
            ]
        )->name('users.edit');


        Route::put(
            '/users/{user}',
            [
                SuperAdminUserController::class,
                'update',
            ]
        )->name('users.update');


        Route::patch(
            '/users/{user}/toggle-status',
            [
                SuperAdminUserController::class,
                'toggleStatus',
            ]
        )->name('users.toggle-status');


        Route::delete(
            '/users/{user}',
            [
                SuperAdminUserController::class,
                'destroy',
            ]
        )->name('users.destroy');


        /*
        |--------------------------------------------------------------------------
        | PLANS D'ABONNEMENT
        |--------------------------------------------------------------------------
        */

        Route::prefix('subscription-plans')
            ->name('subscription-plans.')
            ->group(function () {

                Route::get(
                    '/',
                    [
                        SubscriptionPlanController::class,
                        'index',
                    ]
                )->name('index');


                Route::get(
                    '/create',
                    [
                        SubscriptionPlanController::class,
                        'create',
                    ]
                )->name('create');


                Route::post(
                    '/',
                    [
                        SubscriptionPlanController::class,
                        'store',
                    ]
                )->name('store');


                Route::get(
                    '/{subscriptionPlan}/edit',
                    [
                        SubscriptionPlanController::class,
                        'edit',
                    ]
                )->name('edit');


                Route::put(
                    '/{subscriptionPlan}',
                    [
                        SubscriptionPlanController::class,
                        'update',
                    ]
                )->name('update');


                Route::patch(
                    '/{subscriptionPlan}/toggle-status',
                    [
                        SubscriptionPlanController::class,
                        'toggleStatus',
                    ]
                )->name('toggle-status');


                Route::patch(
                    '/{subscriptionPlan}/toggle-featured',
                    [
                        SubscriptionPlanController::class,
                        'toggleFeatured',
                    ]
                )->name('toggle-featured');


                Route::delete(
                    '/{subscriptionPlan}',
                    [
                        SubscriptionPlanController::class,
                        'destroy',
                    ]
                )->name('destroy');

            });


        /*
        |--------------------------------------------------------------------------
        | ABONNEMENTS
        |--------------------------------------------------------------------------
        */

        Route::prefix('subscriptions')
            ->name('subscriptions.')
            ->group(function () {

                Route::get(
                    '/',
                    [
                        SubscriptionController::class,
                        'index',
                    ]
                )->name('index');


                Route::get(
                    '/create',
                    [
                        SubscriptionController::class,
                        'create',
                    ]
                )->name('create');


                Route::post(
                    '/',
                    [
                        SubscriptionController::class,
                        'store',
                    ]
                )->name('store');


                Route::get(
                    '/{subscription}',
                    [
                        SubscriptionController::class,
                        'show',
                    ]
                )->name('show');


                Route::get(
                    '/{subscription}/edit',
                    [
                        SubscriptionController::class,
                        'edit',
                    ]
                )->name('edit');


                Route::put(
                    '/{subscription}',
                    [
                        SubscriptionController::class,
                        'update',
                    ]
                )->name('update');


                Route::patch(
                    '/{subscription}/cancel',
                    [
                        SubscriptionController::class,
                        'cancel',
                    ]
                )->name('cancel');

            });


        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/payments',
            [
                PaymentController::class,
                'index',
            ]
        )->name('payments.index');


        Route::get(
            '/payments/create',
            [
                PaymentController::class,
                'create',
            ]
        )->name('payments.create');


        Route::get(
            '/payments/{payment}/edit',
            [
                PaymentController::class,
                'edit',
            ]
        )->name('payments.edit');


        Route::put(
            '/payments/{payment}',
            [
                PaymentController::class,
                'update',
            ]
        )->name('payments.update');


        Route::post(
            '/payments',
            [
                PaymentController::class,
                'store',
            ]
        )->name('payments.store');


        Route::get(
            '/payments/{payment}/receipt',
            [
                PaymentController::class,
                'receipt',
            ]
        )->name('payments.receipt');


        Route::get(
            '/payments/{payment}',
            [
                PaymentController::class,
                'show',
            ]
        )->name('payments.show');


        /*
        |--------------------------------------------------------------------------
        | PARAMÈTRES SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/settings',
            [
                PlatformSettingController::class,
                'index',
            ]
        )->name('settings.index');


        Route::put(
            '/settings',
            [
                PlatformSettingController::class,
                'update',
            ]
        )->name('settings.update');


        Route::delete(
            '/settings/logo',
            [
                PlatformSettingController::class,
                'removeLogo',
            ]
        )->name('settings.remove-logo');


        Route::delete(
            '/settings/favicon',
            [
                PlatformSettingController::class,
                'removeFavicon',
            ]
        )->name('settings.remove-favicon');

    });


/*
|--------------------------------------------------------------------------
| ESPACE ENTREPRISE
|--------------------------------------------------------------------------
|
| Toutes les routes entreprise passent par :
|
| auth
| companyadmin
|
*/

Route::middleware([
    'auth',
    'companyadmin',
])
    ->prefix('company')
    ->name('company.')
    ->group(function () {


        /*
        |--------------------------------------------------------------------------
        | SÉLECTEUR DE MODULES
        |--------------------------------------------------------------------------
        */

        Route::get('/modules', [
            ModuleLauncherController::class,
            'index'
        ])->name('modules.index');


        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        |
        | Le dashboard reste toujours accessible.
        |
        */

        Route::get(
            '/dashboard',
            [
                CompanyDashboardController::class,
                'index',
            ]
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | MODULE : CATÉGORIES
        |--------------------------------------------------------------------------
        */

        Route::middleware('module_permission:categories.view')
            ->group(function () {

                Route::get(
                    '/categories',
                    [
                        CategoryController::class,
                        'index',
                    ]
                )->name('categories.index');


                Route::get(
                    '/categories/{category}',
                    [
                        CategoryController::class,
                        'show',
                    ]
                )->whereNumber('category')->name('categories.show');

            });


        Route::middleware('module_permission:categories.create')
            ->group(function () {

                Route::get(
                    '/categories/create',
                    [
                        CategoryController::class,
                        'create',
                    ]
                )->name('categories.create');


                Route::post(
                    '/categories',
                    [
                        CategoryController::class,
                        'store',
                    ]
                )->name('categories.store');

            });


        Route::middleware('module_permission:categories.edit')
            ->group(function () {

                Route::get(
                    '/categories/{category}/edit',
                    [
                        CategoryController::class,
                        'edit',
                    ]
                )->name('categories.edit');


                Route::put(
                    '/categories/{category}',
                    [
                        CategoryController::class,
                        'update',
                    ]
                )->name('categories.update');


                Route::patch(
                    '/categories/{category}/toggle-status',
                    [
                        CategoryController::class,
                        'toggleStatus',
                    ]
                )->name('categories.toggle-status');

            });


        Route::middleware('module_permission:categories.delete')
            ->group(function () {

                Route::delete(
                    '/categories/{category}',
                    [
                        CategoryController::class,
                        'destroy',
                    ]
                )->name('categories.destroy');

            });


        /*
        |--------------------------------------------------------------------------
        | MODULE : PRODUITS
        |--------------------------------------------------------------------------
        */

        Route::middleware('module_permission:products.view')
            ->group(function () {

                Route::get(
                    '/products',
                    [
                        ProductController::class,
                        'index',
                    ]
                )->name('products.index');


                
                Route::get(
                    '/products/{product}',
                    [
                        ProductController::class,
                        'show',
                    ]
                )->whereNumber('product')->name('products.show');


            });


        Route::middleware('module_permission:products.create')
            ->group(function () {

                Route::get(
                    '/products/create',
                    [
                        ProductController::class,
                        'create',
                    ]
                )->name('products.create');


                Route::post(
                    '/products',
                    [
                        ProductController::class,
                        'store',
                    ]
                )->name('products.store');

            });


        Route::middleware('module_permission:products.edit')
            ->group(function () {

                Route::get(
                    '/products/{product}/edit',
                    [
                        ProductController::class,
                        'edit',
                    ]
                )->name('products.edit');


                Route::put(
                    '/products/{product}',
                    [
                        ProductController::class,
                        'update',
                    ]
                )->name('products.update');

            });


        Route::middleware('module_permission:products.delete')
            ->group(function () {

                Route::delete(
                    '/products/{product}',
                    [
                        ProductController::class,
                        'destroy',
                    ]
                )->name('products.destroy');

            });


        /*
        |--------------------------------------------------------------------------
        | MODULE : STOCK
        |--------------------------------------------------------------------------
        */

        Route::middleware('module_permission:stock.view')
            ->group(function () {

                Route::get(
                    '/stock',
                    [
                        StockController::class,
                        'index',
                    ]
                )->name('stock.index');


                Route::get(
                    '/stock/history',
                    [
                        StockController::class,
                        'history',
                    ]
                )->name('stock.history');

            });


        Route::middleware('module_permission:stock.entry')
            ->group(function () {

                Route::get(
                    '/stock/entry',
                    [
                        StockController::class,
                        'createEntry',
                    ]
                )->name('stock.entry');


                Route::post(
                    '/stock/entry',
                    [
                        StockController::class,
                        'storeEntry',
                    ]
                )->name('stock.entry.store');

            });


        Route::middleware('module_permission:stock.exit')
            ->group(function () {

                Route::get(
                    '/stock/exit',
                    [
                        StockController::class,
                        'createExit',
                    ]
                )->name('stock.exit');


                Route::post(
                    '/stock/exit',
                    [
                        StockController::class,
                        'storeExit',
                    ]
                )->name('stock.exit.store');

            });


        /*
        |--------------------------------------------------------------------------
        | MODULE : CLIENTS
        |--------------------------------------------------------------------------
        */

        Route::middleware('module_permission:clients.view')
            ->group(function () {

                Route::get(
                    '/clients',
                    [
                        ClientController::class,
                        'index',
                    ]
                )->name('clients.index');


                Route::get(
                    '/clients/{client}',
                    [
                        ClientController::class,
                        'show',
                    ]
                )->whereNumber('client')->name('clients.show');

            });


        Route::middleware('module_permission:clients.create')
            ->group(function () {

                Route::get(
                    '/clients/create',
                    [
                        ClientController::class,
                        'create',
                    ]
                )->name('clients.create');


                Route::post(
                    '/clients',
                    [
                        ClientController::class,
                        'store',
                    ]
                )->name('clients.store');

            });


        Route::middleware('module_permission:clients.edit')
            ->group(function () {

                Route::get(
                    '/clients/{client}/edit',
                    [
                        ClientController::class,
                        'edit',
                    ]
                )->name('clients.edit');


                Route::put(
                    '/clients/{client}',
                    [
                        ClientController::class,
                        'update',
                    ]
                )->name('clients.update');

            });


        Route::middleware('module_permission:clients.delete')
            ->group(function () {

                Route::delete(
                    '/clients/{client}',
                    [
                        ClientController::class,
                        'destroy',
                    ]
                )->name('clients.destroy');

            });


        /*
        |--------------------------------------------------------------------------
        | MODULE : FOURNISSEURS
        |--------------------------------------------------------------------------
        */

        Route::middleware('module_permission:suppliers.view')
            ->group(function () {

                Route::get(
                    '/suppliers',
                    [
                        SupplierController::class,
                        'index',
                    ]
                )->name('suppliers.index');


                Route::get(
                    '/suppliers/{supplier}',
                    [
                        SupplierController::class,
                        'show',
                    ]
                )->whereNumber('supplier')->name('suppliers.show');

            });


        Route::middleware('module_permission:suppliers.create')
            ->group(function () {

                Route::get(
                    '/suppliers/create',
                    [
                        SupplierController::class,
                        'create',
                    ]
                )->name('suppliers.create');


                Route::post(
                    '/suppliers',
                    [
                        SupplierController::class,
                        'store',
                    ]
                )->name('suppliers.store');

            });


        Route::middleware('module_permission:suppliers.edit')
            ->group(function () {

                Route::get(
                    '/suppliers/{supplier}/edit',
                    [
                        SupplierController::class,
                        'edit',
                    ]
                )->name('suppliers.edit');


                Route::put(
                    '/suppliers/{supplier}',
                    [
                        SupplierController::class,
                        'update',
                    ]
                )->name('suppliers.update');

            });


        Route::middleware('module_permission:suppliers.delete')
            ->group(function () {

                Route::delete(
                    '/suppliers/{supplier}',
                    [
                        SupplierController::class,
                        'destroy',
                    ]
                )->name('suppliers.destroy');

            });


        /*
        |--------------------------------------------------------------------------
        | MODULE : VENTES
        |--------------------------------------------------------------------------
        */

        Route::middleware('module_permission:sales.view')
            ->group(function () {

                Route::get(
                    '/sales',
                    [
                        SaleController::class,
                        'index',
                    ]
                )->name('sales.index');


                Route::get(
                    '/sales/{sale}/invoice',
                    [
                        SaleController::class,
                        'invoice',
                    ]
                )->name('sales.invoice');


                Route::get(
                    '/sales/{sale}',
                    [
                        SaleController::class,
                        'show',
                    ]
                )->whereNumber('sale')->name('sales.show');

            });


        Route::middleware('module_permission:sales.create')
            ->group(function () {

                Route::get(
                    '/sales/create',
                    [
                        SaleController::class,
                        'create',
                    ]
                )->name('sales.create');


                Route::post(
                    '/sales',
                    [
                        SaleController::class,
                        'store',
                    ]
                )->name('sales.store');

            });


        Route::middleware('module_permission:sales.cancel')
            ->group(function () {

                Route::delete(
                    '/sales/{sale}',
                    [
                        SaleController::class,
                        'destroy',
                    ]
                )->name('sales.destroy');

            });


        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS DES VENTES
        |--------------------------------------------------------------------------
        |
        | La vente ET le module Paiements doivent être actifs.
        |
        */

        Route::middleware([
            'module:sales',
            'module_permission:payments.create',
        ])
            ->group(function () {

                Route::post(
                    '/sales/{sale}/payments',
                    [
                        SalePaymentController::class,
                        'store',
                    ]
                )->name('sales.payments.store');

            });


        Route::middleware([
            'module:sales',
            'module_permission:payments.cancel',
        ])
            ->group(function () {

                Route::delete(
                    '/sale-payments/{payment}',
                    [
                        SalePaymentController::class,
                        'destroy',
                    ]
                )->name('sales.payments.destroy');

            });


        /*
        |--------------------------------------------------------------------------
        | MODULE : ACHATS
        |--------------------------------------------------------------------------
        |
        | Aucun edit/update pour les achats.
        |
        */

        Route::middleware('module_permission:purchases.view')
            ->group(function () {

                Route::get(
                    '/purchases',
                    [
                        PurchaseController::class,
                        'index',
                    ]
                )->name('purchases.index');


                Route::get(
                    '/purchases/{purchase}',
                    [
                        PurchaseController::class,
                        'show',
                    ]
                )->whereNumber('purchase')->name('purchases.show');


                Route::get(
                    '/purchases/{purchase}/invoice',
                    [
                        PurchaseController::class,
                        'invoice',
                    ]
                )->name('purchases.invoice');

            });


        Route::middleware('module_permission:purchases.create')
            ->group(function () {

                Route::get(
                    '/purchases/create',
                    [
                        PurchaseController::class,
                        'create',
                    ]
                )->name('purchases.create');


                Route::post(
                    '/purchases',
                    [
                        PurchaseController::class,
                        'store',
                    ]
                )->name('purchases.store');

            });


        Route::middleware('module_permission:purchases.cancel')
            ->group(function () {

                Route::delete(
                    '/purchases/{purchase}',
                    [
                        PurchaseController::class,
                        'destroy',
                    ]
                )->name('purchases.destroy');

            });


        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS DES ACHATS
        |--------------------------------------------------------------------------
        |
        | L'achat ET le module Paiements doivent être actifs.
        |
        */

        Route::middleware([
            'module:purchases',
            'module_permission:payments.create',
        ])
            ->group(function () {

                Route::post(
                    '/purchases/{purchase}/payments',
                    [
                        PurchasePaymentController::class,
                        'store',
                    ]
                )->name('purchases.payments.store');

            });


        Route::middleware([
            'module:purchases',
            'module_permission:payments.cancel',
        ])
            ->group(function () {

                Route::delete(
                    '/purchase-payments/{payment}',
                    [
                        PurchasePaymentController::class,
                        'destroy',
                    ]
                )->name('purchases.payments.destroy');

            });


        /*
        |--------------------------------------------------------------------------
        | MODULE : PAIEMENTS
        |--------------------------------------------------------------------------
        |
        | Le module existe déjà dans l'architecture.
        | Les routes indépendantes pourront être ajoutées ici.
        |
        */

        /*
        |--------------------------------------------------------------------------
        | MODULE : FINANCES
        |--------------------------------------------------------------------------
        */

        Route::middleware('module_permission:finance.view')
            ->group(function () {

                Route::get(
                    '/finance',
                    [
                        FinanceController::class,
                        'index',
                    ]
                )->name('finance.index');

            });


        /*
        |--------------------------------------------------------------------------
        | MODULE : RAPPORTS
        |--------------------------------------------------------------------------
        */

        Route::middleware('module_permission:reports.view')
            ->group(function () {

                Route::get(
                    '/reports',
                    [
                        ReportController::class,
                        'index',
                    ]
                )->name('reports.index');

            });


        Route::middleware('module_permission:reports.export')
            ->group(function () {

                Route::get(
                    '/reports/export/excel',
                    [
                        ReportExportController::class,
                        'excel',
                    ]
                )->name('reports.export.excel');


                Route::get(
                    '/reports/export/pdf',
                    [
                        ReportExportController::class,
                        'pdf',
                    ]
                )->name('reports.export.pdf');

            });


        /*
        |--------------------------------------------------------------------------
        | MODULE : PRESTATIONS
        |--------------------------------------------------------------------------
        */

        Route::middleware('module_permission:services.view')
            ->group(function () {

                Route::get(
                    '/services',
                    [
                        ServiceController::class,
                        'index',
                    ]
                )->name('services.index');

            });


        Route::middleware('module_permission:services.create')
            ->group(function () {

                Route::get(
                    '/services/create',
                    [
                        ServiceController::class,
                        'create',
                    ]
                )->name('services.create');


                Route::post(
                    '/services',
                    [
                        ServiceController::class,
                        'store',
                    ]
                )->name('services.store');

            });


        Route::middleware('module_permission:services.edit')
            ->group(function () {

                Route::get(
                    '/services/{service}/edit',
                    [
                        ServiceController::class,
                        'edit',
                    ]
                )->name('services.edit');


                Route::put(
                    '/services/{service}',
                    [
                        ServiceController::class,
                        'update',
                    ]
                )->name('services.update');


                Route::patch(
                    '/services/{service}/toggle-status',
                    [
                        ServiceController::class,
                        'toggleStatus',
                    ]
                )->name('services.toggle-status');

            });

            /*
            |--------------------------------------------------------------------------
            | MODULE : SITE WEB
            |--------------------------------------------------------------------------
            */

            Route::middleware([
                'module:website',
                'module_permission:website.view',
            ])->group(function () {
                Route::get(
                    '/website',
                    [WebsiteController::class, 'index']
                )->name('website.index');
                
                    Route::get('/website/media/picker', [
                        WebsiteMediaPickerController::class,
                        'index'
                    ])->name('website.media.picker');

            });

            

/*
|--------------------------------------------------------------------------
| MODULE SITE WEB — GESTION DES PAGES
|--------------------------------------------------------------------------
*/

Route::middleware([
    'module:website',
    'module_permission:website.view',
])
->prefix('website')
->name('website.')
->group(function () {

    Route::get('/pages', [
        WebsitePageController::class,
        'index'
    ])->name('pages.index');

    Route::get('/pages/create', [
        WebsitePageController::class,
        'create'
    ])->name('pages.create');

    Route::post('/pages', [
        WebsitePageController::class,
        'store'
    ])->name('pages.store');

    Route::get('/pages/{page}/edit', [
        WebsitePageController::class,
        'edit'
    ])
    ->whereNumber('page')
    ->name('pages.edit');

    Route::put('/pages/{page}', [
        WebsitePageController::class,
        'update'
    ])
    ->whereNumber('page')
    ->name('pages.update');

    Route::delete('/pages/{page}', [
        WebsitePageController::class,
        'destroy'
    ])
    ->whereNumber('page')
    ->name('pages.destroy');

    Route::get('/pages/{page}/preview', [
        WebsitePageController::class,
        'preview'
    ])
    ->whereNumber('page')
    ->name('pages.preview');

});


/*
|--------------------------------------------------------------------------
| MODULE SITE WEB — MÉDIATHÈQUE
|--------------------------------------------------------------------------
*/

Route::middleware([
    'module:website',
    'module_permission:website.view',
])
->prefix('website/media')
->name('website.media.')
->group(function () {

    Route::get('/', [
        WebsiteMediaController::class,
        'index'
    ])->name('index');

    Route::post('/', [
        WebsiteMediaController::class,
        'store'
    ])->name('store');

    Route::delete('/{media}', [
        WebsiteMediaController::class,
        'destroy'
    ])
    ->whereNumber('media')
    ->name('destroy');

});


/*
|--------------------------------------------------------------------------
| MODULE SITE WEB — MENUS DYNAMIQUES
|--------------------------------------------------------------------------
*/

Route::middleware([
    'module:website',
    'module_permission:website.view',
])
->prefix('website/menus')
->name('website.menus.')
->controller(WebsiteMenuController::class)
->group(function () {

    Route::get('/', 'index')
        ->name('index');

    Route::post('/', 'store')
        ->name('store');

    Route::put('/{menu}', 'update')
        ->whereNumber('menu')
        ->name('update');

    Route::post('/{menu}/move/{direction}', 'move')
        ->whereNumber('menu')
        ->whereIn('direction', ['up', 'down'])
        ->name('move');

    Route::delete('/{menu}', 'destroy')
        ->whereNumber('menu')
        ->name('destroy');

});


/*
|--------------------------------------------------------------------------
| WEBSTUDIO — IDENTITÉ VISUELLE
|--------------------------------------------------------------------------
*/

Route::middleware([
    'module:website',
    'module_permission:website.view',
])
->prefix('website/settings')
->name('website.settings.')
->controller(WebsiteSettingController::class)
->group(function () {

    Route::get('/', 'edit')
        ->name('edit');

    Route::put('/', 'update')
        ->name('update');

});


/*
|--------------------------------------------------------------------------
| WEBSTUDIO — MODÈLES DE SITES
|--------------------------------------------------------------------------
*/

Route::middleware([
    'module:website',
    'module_permission:website.view',
])
->prefix('website/templates')
->name('website.templates.')
->controller(WebsiteTemplateController::class)
->group(function () {

    Route::get('/', 'index')
        ->name('index');

        Route::get('/{template}/preview', 'preview')
    ->name('preview');

    Route::post('/{template}/install', 'install')
        ->name('install');

});

Route::middleware(['module:website', 'module_permission:website.view'])
    ->prefix('website')
    ->name('website.')
    ->group(function () {
        Route::get('/pages/{page}/builder', [WebsiteBuilderController::class, 'edit'])
            ->whereNumber('page')->name('builder.edit');
        Route::put('/pages/{page}/builder', [WebsiteBuilderController::class, 'update'])
            ->whereNumber('page')->name('builder.update');
    });


    



            /*
            |--------------------------------------------------------------------------
            | E-COMMERCE
            |--------------------------------------------------------------------------
            */

            Route::middleware('module:ecommerce')
                ->prefix('ecommerce')
                ->name('ecommerce.')
                ->group(function () {

                    Route::middleware(
                        'module_permission:ecommerce.view'
                    )->group(function () {

                        Route::get(
                            '/',
                            [EcommerceController::class, 'index']
                        )->name('index');

                        Route::get(
                            '/products',
                            [EcommerceController::class, 'products']
                        )->name('products.index');
                    });


                    Route::middleware(
                        'module_permission:ecommerce.create'
                    )->group(function () {

                        Route::get(
                            '/products/create',
                            [EcommerceController::class, 'create']
                        )->name('products.create');

                        Route::post(
                            '/products',
                            [EcommerceController::class, 'store']
                        )->name('products.store');
                    });


                    Route::middleware(
                        'module_permission:ecommerce.edit'
                    )->group(function () {

                        Route::get(
                            '/products/{ecommerceProduct}/edit',
                            [EcommerceController::class, 'edit']
                        )->name('products.edit');

                        Route::put(
                            '/products/{ecommerceProduct}',
                            [EcommerceController::class, 'update']
                        )->name('products.update');
                    });


                    Route::middleware(
                        'module_permission:ecommerce.delete'
                    )->group(function () {

                        Route::delete(
                            '/products/{ecommerceProduct}',
                            [EcommerceController::class, 'destroy']
                        )->name('products.destroy');
                    });
                });


                    Route::middleware('module_permission:services.delete')
                        ->group(function () {

                            Route::delete(
                                '/services/{service}',
                                [
                                    ServiceController::class,
                                    'destroy',
                                ]
                            )->name('services.destroy');

                        });

            /*
        |--------------------------------------------------------------------------
        | E-COMMERCE — COMMANDES
        |--------------------------------------------------------------------------
        */

        Route::middleware('module:ecommerce')
            ->prefix('ecommerce')
            ->name('ecommerce.')
            ->group(function () {

                /*
                |--------------------------------------------------------------------------
                | CONSULTATION
                |--------------------------------------------------------------------------
                */

                Route::middleware('module_permission:ecommerce.view')
                    ->group(function () {

                        Route::get('/orders', [
                            \App\Http\Controllers\CompanyAdmin\EcommerceOrderController::class,
                            'index'
                        ])->name('orders.index');

                        Route::get('/orders/{order}', [
                            \App\Http\Controllers\CompanyAdmin\EcommerceOrderController::class,
                            'show'
                        ])->whereNumber('order')->name('orders.show');
                    });


                /*
                |--------------------------------------------------------------------------
                | CRÉATION
                |--------------------------------------------------------------------------
                */

                Route::middleware('module_permission:ecommerce.create')
                    ->group(function () {

                        Route::get('/orders/create', [
                            \App\Http\Controllers\CompanyAdmin\EcommerceOrderController::class,
                            'create'
                        ])->name('orders.create');

                        Route::post('/orders', [
                            \App\Http\Controllers\CompanyAdmin\EcommerceOrderController::class,
                            'store'
                        ])->name('orders.store');
                    });


                /*
                |--------------------------------------------------------------------------
                | MODIFICATION / STATUT
                |--------------------------------------------------------------------------
                */

                Route::middleware('module_permission:ecommerce.edit')
                    ->group(function () {

                        Route::patch('/orders/{order}/status', [
                            \App\Http\Controllers\CompanyAdmin\EcommerceOrderController::class,
                            'updateStatus'
                        ])->name('orders.status');
                    });


                /*
                |--------------------------------------------------------------------------
                | SUPPRESSION / ANNULATION
                |--------------------------------------------------------------------------
                */

                Route::middleware('module_permission:ecommerce.delete')
                    ->group(function () {

                        Route::delete('/orders/{order}', [
                            \App\Http\Controllers\CompanyAdmin\EcommerceOrderController::class,
                            'destroy'
                        ])->name('orders.destroy');
                    });
            });

    /*
        |--------------------------------------------------------------------------
        | E-COMMERCE — CLIENTS
        |--------------------------------------------------------------------------
        */

        Route::middleware('module:ecommerce')
            ->prefix('ecommerce')
            ->name('ecommerce.')
            ->group(function () {

                Route::middleware('module_permission:ecommerce.view')
                    ->group(function () {

                        Route::get('/clients', [
                            \App\Http\Controllers\CompanyAdmin\EcommerceClientController::class,
                            'index'
                        ])->name('clients.index');

                        Route::get('/clients/{client}', [
                            \App\Http\Controllers\CompanyAdmin\EcommerceClientController::class,
                            'show'
                        ])->name('clients.show');
                    });
            });

    /*
        |--------------------------------------------------------------------------
        | E-COMMERCE — PAIEMENTS
        |--------------------------------------------------------------------------
        */

        Route::middleware('module:ecommerce')
            ->prefix('ecommerce')
            ->name('ecommerce.')
            ->group(function () {

                Route::middleware('module_permission:ecommerce.view')->group(function () {
                    Route::get('/payments', [
                        \App\Http\Controllers\CompanyAdmin\EcommercePaymentController::class,
                        'index'
                    ])->name('payments.index');

                    Route::get('/payments/{payment}', [
                        \App\Http\Controllers\CompanyAdmin\EcommercePaymentController::class,
                        'show'
                    ])->whereNumber('payment')
                    ->name('payments.show');
                });

                Route::middleware('module_permission:ecommerce.create')->group(function () {
                    // Nouveau paiement : route déclarée explicitement
                    Route::get('/payments/create', [
                        \App\Http\Controllers\CompanyAdmin\EcommercePaymentController::class,
                        'create'
                    ])->name('payments.create');

                    Route::post('/orders/{order}/payments', [
                        \App\Http\Controllers\CompanyAdmin\EcommercePaymentController::class,
                        'store'
                    ])->whereNumber('order')
                    ->name('orders.payments.store');
                });

                Route::middleware('module_permission:ecommerce.delete')->group(function () {
                    Route::delete('/payments/{payment}', [
                        \App\Http\Controllers\CompanyAdmin\EcommercePaymentController::class,
                        'destroy'
                    ])->whereNumber('payment')
                    ->name('payments.destroy');
                });
            });


            /*==========================================================
               LIVRAISON
            ============================================================*/

            Route::prefix('ecommerce')->name('ecommerce.')
    ->middleware('module:ecommerce')
    ->group(function () {
        Route::get('/deliveries', [\App\Http\Controllers\CompanyAdmin\EcommerceDeliveryController::class, 'index'])
            ->middleware('module_permission:ecommerce.view')->name('deliveries.index');

        Route::get('/deliveries/create', [\App\Http\Controllers\CompanyAdmin\EcommerceDeliveryController::class, 'create'])
            ->middleware('module_permission:ecommerce.create')->name('deliveries.create');

        Route::post('/deliveries', [\App\Http\Controllers\CompanyAdmin\EcommerceDeliveryController::class, 'store'])
            ->middleware('module_permission:ecommerce.create')->name('deliveries.store');

        Route::get('/deliveries/{delivery}', [\App\Http\Controllers\CompanyAdmin\EcommerceDeliveryController::class, 'show'])
            ->whereNumber('delivery')->middleware('module_permission:ecommerce.view')->name('deliveries.show');

        Route::get('/deliveries/{delivery}/edit', [\App\Http\Controllers\CompanyAdmin\EcommerceDeliveryController::class, 'edit'])
            ->whereNumber('delivery')->middleware('module_permission:ecommerce.edit')->name('deliveries.edit');

        Route::put('/deliveries/{delivery}', [\App\Http\Controllers\CompanyAdmin\EcommerceDeliveryController::class, 'update'])
            ->whereNumber('delivery')->middleware('module_permission:ecommerce.edit')->name('deliveries.update');

        Route::patch('/deliveries/{delivery}/status', [\App\Http\Controllers\CompanyAdmin\EcommerceDeliveryController::class, 'updateStatus'])
            ->whereNumber('delivery')->middleware('module_permission:ecommerce.edit')->name('deliveries.status');

        Route::delete('/deliveries/{delivery}', [\App\Http\Controllers\CompanyAdmin\EcommerceDeliveryController::class, 'destroy'])
            ->whereNumber('delivery')->middleware('module_permission:ecommerce.delete')->name('deliveries.destroy');
    });

    /*========================================================
    PROMOTIONS 
    =========================================================*/
     Route::middleware(['module:ecommerce'])
    ->prefix('ecommerce')
    ->name('ecommerce.')
    ->group(function () {
        Route::get('/promotions',
            [EcommercePromotionController::class, 'index']
        )->name('promotions.index');

        Route::get('/promotions/create',
            [EcommercePromotionController::class, 'create']
        )->name('promotions.create');

        Route::post('/promotions',
            [EcommercePromotionController::class, 'store']
        )->name('promotions.store');

        Route::get('/promotions/{promotion}/edit',
            [EcommercePromotionController::class, 'edit']
        )->name('promotions.edit');

        Route::put('/promotions/{promotion}',
            [EcommercePromotionController::class, 'update']
        )->name('promotions.update');

        Route::patch('/promotions/{promotion}/toggle',
            [EcommercePromotionController::class, 'toggle']
        )->name('promotions.toggle');

        Route::delete('/promotions/{promotion}',
            [EcommercePromotionController::class, 'destroy']
        )->name('promotions.destroy');
    });

    /*====================================
        AVIS CLIENTS
   ============================================= */

    
Route::prefix('ecommerce')
    ->name('ecommerce.')
    ->middleware(['module:ecommerce'])
    ->group(function () {
        Route::get('/reviews', [
            EcommerceReviewController::class,
            'index'
        ])->name('reviews.index');

        Route::get('/reviews/{review}', [
            EcommerceReviewController::class,
            'show'
        ])->name('reviews.show');

        Route::patch('/reviews/{review}/status', [
            EcommerceReviewController::class,
            'updateStatus'
        ])->name('reviews.status');

        Route::delete('/reviews/{review}', [
            EcommerceReviewController::class,
            'destroy'
        ])->name('reviews.destroy');
    });





/*
|--------------------------------------------------------------------------
| E-COMMERCE — PARAMÈTRES BOUTIQUE
|--------------------------------------------------------------------------
*/

Route::middleware(['module:ecommerce'])
    ->prefix('ecommerce')
    ->name('ecommerce.')
    ->group(function () {

        Route::get('/settings', [
            EcommerceStoreSettingController::class,
            'edit'
        ])
        ->middleware('module_permission:ecommerce.view')
        ->name('settings.edit');

        Route::put('/settings', [
            EcommerceStoreSettingController::class,
            'update'
        ])
        ->middleware('module_permission:ecommerce.edit')
        ->name('settings.update');

    });

    
/*
|--------------------------------------------------------------------------
| E-COMMERCE — RAPPORTS
|--------------------------------------------------------------------------
*/

Route::middleware([
    'module:ecommerce',
    'module_permission:ecommerce.view',
])
    ->prefix('ecommerce')
    ->name('ecommerce.')
    ->group(function () {

        Route::get('/reports', [
            EcommerceReportController::class,
            'index'
        ])->name('reports.index');



        Route::get('/reports/export/pdf', [
    EcommerceReportExportController::class,
    'pdf'
])->name('reports.export.pdf');

Route::get('/reports/export/excel', [
    EcommerceReportExportController::class,
    'excel'
])->name('reports.export.excel');

    });


    
/*
|--------------------------------------------------------------------------
| E-COMMERCE — CATEGORIES
|--------------------------------------------------------------------------
*/

Route::middleware(['module:ecommerce'])
    ->prefix('ecommerce/categories')
    ->name('ecommerce.categories.')
    ->group(function () {

        // LISTE DES CATEGORIES
        Route::get('/', [
            EcommerceCategoryController::class,
            'index'
        ])
        ->middleware('module_permission:ecommerce.view')
        ->name('index');


        // FORMULAIRE DE CREATION
        Route::get('/create', [
            EcommerceCategoryController::class,
            'create'
        ])
        ->middleware('module_permission:ecommerce.create')
        ->name('create');


        // ENREGISTRER UNE CATEGORIE
        Route::post('/', [
            EcommerceCategoryController::class,
            'store'
        ])
        ->middleware('module_permission:ecommerce.create')
        ->name('store');


        // FORMULAIRE DE MODIFICATION
        Route::get('/{id}/edit', [
            EcommerceCategoryController::class,
            'edit'
        ])
        ->whereNumber('id')
        ->middleware('module_permission:ecommerce.edit')
        ->name('edit');


        // MODIFIER UNE CATEGORIE
        Route::put('/{id}', [
            EcommerceCategoryController::class,
            'update'
        ])
        ->whereNumber('id')
        ->middleware('module_permission:ecommerce.edit')
        ->name('update');


        // ACTIVER / DESACTIVER
        Route::patch('/{id}/toggle-status', [
            EcommerceCategoryController::class,
            'toggleStatus'
        ])
        ->whereNumber('id')
        ->middleware('module_permission:ecommerce.edit')
        ->name('toggle-status');


        // SUPPRIMER UNE CATEGORIE
        Route::delete('/{id}', [
            EcommerceCategoryController::class,
            'destroy'
        ])
        ->whereNumber('id')
        ->middleware('module_permission:ecommerce.delete')
        ->name('destroy');

    });

    
/*
|--------------------------------------------------------------------------
| E-COMMERCE — COLLECTIONS
|--------------------------------------------------------------------------
*/

Route::middleware(['module:ecommerce'])
    ->prefix('ecommerce/collections')
    ->name('ecommerce.collections.')
    ->group(function () {

        Route::get(
            '/',
            [EcommerceCollectionController::class, 'index']
        )
        ->middleware('module_permission:ecommerce.view')
        ->name('index');

        Route::get(
            '/create',
            [EcommerceCollectionController::class, 'create']
        )
        ->middleware('module_permission:ecommerce.create')
        ->name('create');

        Route::post(
            '/',
            [EcommerceCollectionController::class, 'store']
        )
        ->middleware('module_permission:ecommerce.create')
        ->name('store');

        Route::get(
            '/{id}/edit',
            [EcommerceCollectionController::class, 'edit']
        )
        ->whereNumber('id')
        ->middleware('module_permission:ecommerce.edit')
        ->name('edit');

        Route::put(
            '/{id}',
            [EcommerceCollectionController::class, 'update']
        )
        ->whereNumber('id')
        ->middleware('module_permission:ecommerce.edit')
        ->name('update');

        Route::patch(
            '/{id}/toggle-status',
            [EcommerceCollectionController::class, 'toggleStatus']
        )
        ->whereNumber('id')
        ->middleware('module_permission:ecommerce.edit')
        ->name('toggle-status');

        Route::delete(
            '/{id}',
            [EcommerceCollectionController::class, 'destroy']
        )
        ->whereNumber('id')
        ->middleware('module_permission:ecommerce.delete')
        ->name('destroy');
    });



    

        /*
        |--------------------------------------------------------------------------
        | UTILISATEURS ENTREPRISE
        |--------------------------------------------------------------------------
        |
        | Les utilisateurs ne dépendent pas d'un module métier.
        |
        */

        Route::middleware('permission:users.view')
            ->group(function () {

                Route::get(
                    '/users',
                    [
                        UserController::class,
                        'index',
                    ]
                )->name('users.index');

            });


        Route::middleware('permission:users.create')
            ->group(function () {

                Route::get(
                    '/users/create',
                    [
                        UserController::class,
                        'create',
                    ]
                )->name('users.create');


                Route::post(
                    '/users',
                    [
                        UserController::class,
                        'store',
                    ]
                )->name('users.store');

            });


        Route::middleware('permission:users.edit')
            ->group(function () {

                Route::get(
                    '/users/{user}/edit',
                    [
                        UserController::class,
                        'edit',
                    ]
                )->name('users.edit');


                Route::put(
                    '/users/{user}',
                    [
                        UserController::class,
                        'update',
                    ]
                )->name('users.update');


                Route::patch(
                    '/users/{user}/toggle-status',
                    [
                        UserController::class,
                        'toggleStatus',
                    ]
                )->name('users.toggle-status');

            });


        Route::middleware('permission:users.delete')
            ->group(function () {

                Route::delete(
                    '/users/{user}',
                    [
                        UserController::class,
                        'destroy',
                    ]
                )->name('users.destroy');

            });


        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS UTILISATEURS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/users/{user}/permissions',
            [
                UserPermissionController::class,
                'edit',
            ]
        )
            ->name('users.permissions.edit')
            ->middleware('permission:users.edit');


        Route::put(
            '/users/{user}/permissions',
            [
                UserPermissionController::class,
                'update',
            ]
        )
            ->name('users.permissions.update')
            ->middleware('permission:users.edit');


        /*
        |--------------------------------------------------------------------------
        | PARAMÈTRES PAIEMENTS
        |--------------------------------------------------------------------------
        |
        | Ici nous gardons volontairement :
        |
        | module:payments
        | permission:settings.*
        |
        | car "settings.view/edit" appartient aux paramètres,
        | et non au module payments dans ModuleRegistry.
        |
        */

        Route::middleware('module:payments')
            ->group(function () {

                Route::middleware('permission:settings.view')
                    ->group(function () {

                        Route::get(
                            '/settings/payments',
                            [
                                PaymentSettingController::class,
                                'edit',
                            ]
                        )->name('settings.payments');

                    });


                Route::middleware('permission:settings.edit')
                    ->group(function () {

                        Route::put(
                            '/settings/payments',
                            [
                                PaymentSettingController::class,
                                'update',
                            ]
                        )->name('settings.payments.update');

                    });

            });


        /*
        |--------------------------------------------------------------------------
        | PARAMÈTRES ENTREPRISE
        |--------------------------------------------------------------------------
        |
        | Les paramètres généraux restent indépendants
        | des modules métier.
        |
        */

        Route::prefix('settings')
            ->name('settings.')
            ->group(function () {

                Route::get(
                    '/',
                    [
                        SettingController::class,
                        'index',
                    ]
                )
                    ->name('index')
                    ->middleware('permission:settings.view');


                Route::put(
                    '/',
                    [
                        SettingController::class,
                        'update',
                    ]
                )
                    ->name('update')
                    ->middleware('permission:settings.edit');


                Route::delete(
                    '/logo',
                    [
                        SettingController::class,
                        'removeLogo',
                    ]
                )
                    ->name('logo.remove')
                    ->middleware('permission:settings.edit');

            });


    /*===============================================
    *                      ECOLE
    ================================================*/
    
Route::middleware([
    'module:school',
    'module_permission:school.view',
])
->prefix('school')
->name('school.')
->group(function () {

    Route::get('/dashboard', [
        \App\Http\Controllers\CompanyAdmin\School\DashboardController::class,
        'index',
    ])->name('dashboard')->middleware('school_permission:school.dashboard.view');

});

Route::middleware(['module:school'])->prefix('school')->name('school.')->group(function () {
    // Conserver UNE SEULE déclaration de dashboard (déjà installée en phase 1).
    // Route::get('/dashboard', [\App\Http\Controllers\CompanyAdmin\School\DashboardController::class,'index'])->name('dashboard');

    $resources = [
        'years' => \App\Http\Controllers\CompanyAdmin\School\YearController::class,
        'sections' => \App\Http\Controllers\CompanyAdmin\School\SectionController::class,
        'levels' => \App\Http\Controllers\CompanyAdmin\School\LevelController::class,
        'classes' => \App\Http\Controllers\CompanyAdmin\School\ClassController::class,
    ];
    foreach ($resources as $segment => $controller) {
        Route::middleware('module_permission:school.view')->group(function () use ($segment,$controller) {
            Route::get($segment, [$controller,'index'])->name($segment.'.index');
        });
        Route::middleware('module_permission:school.create')->group(function () use ($segment,$controller) {
            Route::get($segment.'/create', [$controller,'create'])->name($segment.'.create');
            Route::post($segment, [$controller,'store'])->name($segment.'.store');
        });
        Route::middleware('module_permission:school.edit')->group(function () use ($segment,$controller) {
            Route::get($segment.'/{id}/edit', [$controller,'edit'])->name($segment.'.edit');
            Route::put($segment.'/{id}', [$controller,'update'])->name($segment.'.update');
        });
        Route::middleware('module_permission:school.delete')->group(function () use ($segment,$controller) {
            Route::delete($segment.'/{id}', [$controller,'destroy'])->name($segment.'.destroy');
        });
    }
    Route::middleware('module_permission:school.edit')->group(function () {
        Route::post('years/{id}/activate', [\App\Http\Controllers\CompanyAdmin\School\YearController::class,'activate'])->name('years.activate');
        Route::post('years/{id}/archive', [\App\Http\Controllers\CompanyAdmin\School\YearController::class,'archive'])->name('years.archive');
    });

    // Routes spécifiques AVANT la ressource students.
    Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
    Route::get('students/import-template', [StudentController::class, 'template'])->name('students.template');
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
    Route::resource('students', StudentController::class)->except(['show']);

    Route::get('enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index')->middleware('school_permission:school.enrollments.manage');
    Route::get('enrollments/create', [EnrollmentController::class, 'create'])->name('enrollments.create')->middleware('school_permission:school.enrollments.manage');
    Route::post('enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store')->middleware('school_permission:school.enrollments.manage');

    /*==============================================
    * SCHOOL FEES / TUITION
    ================================================*/

    Route::middleware('module_permission:school.view')->group(function () {
        Route::get('fees', [\App\Http\Controllers\CompanyAdmin\School\FeeStructureController::class, 'index'])->name('fees.index');
        Route::get('tuition', [\App\Http\Controllers\CompanyAdmin\School\SchoolFinanceController::class, 'index'])->name('tuition.index')->middleware('school_permission:school.tuition.manage');
    });

    Route::middleware('module_permission:school.create')->group(function () {
        Route::get('fees/create', [\App\Http\Controllers\CompanyAdmin\School\FeeStructureController::class, 'create'])->name('fees.create');
        Route::post('fees', [\App\Http\Controllers\CompanyAdmin\School\FeeStructureController::class, 'store'])->name('fees.store');
        Route::post('tuition/assign', [\App\Http\Controllers\CompanyAdmin\School\SchoolFinanceController::class, 'assign'])->name('tuition.assign')->middleware('school_permission:school.tuition.manage');
        Route::post('tuition/{id}/payments', [\App\Http\Controllers\CompanyAdmin\School\SchoolFinanceController::class, 'pay'])->name('tuition.pay')->middleware('school_permission:school.tuition.manage');
    });

    Route::middleware('module_permission:school.edit')->group(function () {
        Route::get('fees/{id}/edit', [\App\Http\Controllers\CompanyAdmin\School\FeeStructureController::class, 'edit'])->name('fees.edit');
        Route::put('fees/{id}', [\App\Http\Controllers\CompanyAdmin\School\FeeStructureController::class, 'update'])->name('fees.update');
    });

    Route::middleware('module_permission:school.delete')->group(function () {
        Route::delete('fees/{id}', [\App\Http\Controllers\CompanyAdmin\School\FeeStructureController::class, 'destroy'])->name('fees.destroy');
    });

    
    Route::middleware('module_permission:school.view')->group(function () {
    Route::get('tuition/debts', [\App\Http\Controllers\CompanyAdmin\School\SchoolFinanceFollowUpController::class, 'debts'])->name('tuition.debts')->middleware('school_permission:school.tuition.manage');
    Route::get('tuition/payments', [\App\Http\Controllers\CompanyAdmin\School\SchoolFinanceFollowUpController::class, 'payments'])->name('tuition.payments')->middleware('school_permission:school.tuition.manage');
    Route::get('tuition/payments/{id}/receipt', [\App\Http\Controllers\CompanyAdmin\School\SchoolFinanceFollowUpController::class, 'receipt'])->whereNumber('id')->name('tuition.receipt')->middleware('school_permission:school.tuition.manage');
});


Route::middleware('module_permission:school.view')->group(function () {
    Route::get('tuition/reports', [\App\Http\Controllers\CompanyAdmin\School\SchoolFinanceReportController::class, 'index'])->name('finance-reports.index');
    Route::get('tuition/reports/excel', [\App\Http\Controllers\CompanyAdmin\School\SchoolFinanceReportController::class, 'excel'])->name('finance-reports.excel');
    Route::get('tuition/reports/pdf', [\App\Http\Controllers\CompanyAdmin\School\SchoolFinanceReportController::class, 'pdf'])->name('finance-reports.pdf');
});


$pedagogy = [
    'teachers' => \App\Http\Controllers\CompanyAdmin\School\SchoolTeacherController::class,
    'subject-groups' => \App\Http\Controllers\CompanyAdmin\School\SchoolSubjectGroupController::class,
    'subjects' => \App\Http\Controllers\CompanyAdmin\School\SchoolSubjectController::class,
    'assignments' => \App\Http\Controllers\CompanyAdmin\School\SchoolTeacherAssignmentController::class,
];
foreach ($pedagogy as $segment => $controller) {
    Route::middleware('module_permission:school.view')->get($segment, [$controller, 'index'])->name($segment.'.index');
    Route::middleware('module_permission:school.create')->get($segment.'/create', [$controller, 'create'])->name($segment.'.create');
    Route::middleware('module_permission:school.create')->post($segment, [$controller, 'store'])->name($segment.'.store');
    Route::middleware('module_permission:school.edit')->get($segment.'/{id}/edit', [$controller, 'edit'])->name($segment.'.edit');
    Route::middleware('module_permission:school.edit')->put($segment.'/{id}', [$controller, 'update'])->name($segment.'.update');
    Route::middleware('module_permission:school.delete')->delete($segment.'/{id}', [$controller, 'destroy'])->name($segment.'.destroy');
}

/* ==============================================================
   AFFECTATIONS PAR ENSEIGNANT — page dédiée
   ============================================================== */
Route::get('teachers/{teacherId}/assignments',
    [\App\Http\Controllers\CompanyAdmin\School\SchoolTeacherAssignmentController::class, 'teacher'])
    ->whereNumber('teacherId')
    ->middleware('module_permission:school.view')
    ->name('assignments.teacher');

/* ==============================================================
   MATIÈRES PAR CLASSE — routes sous /company/school
   ============================================================== */
Route::prefix('classes/{classId}/subjects')
    ->whereNumber('classId')
    ->name('classes.subjects.')
    ->group(function () {
        Route::get('/', [ClassSubjectController::class, 'index'])
            ->middleware('module_permission:school.view')
            ->name('index');

        Route::post('/', [ClassSubjectController::class, 'store'])
            ->middleware('module_permission:school.create')
            ->name('store');

        Route::put('/{id}', [ClassSubjectController::class, 'update'])
            ->whereNumber('id')
            ->middleware('module_permission:school.edit')
            ->name('update');

        Route::delete('/{id}', [ClassSubjectController::class, 'destroy'])
            ->whereNumber('id')
            ->middleware('module_permission:school.delete')
            ->name('destroy');
    });


Route::get('grades', [\App\Http\Controllers\CompanyAdmin\School\SchoolGradeController::class,'index'])->middleware('module_permission:school.view')->name('grades.index')->middleware('school_permission:school.grades.view');
Route::post('grades/configure', [\App\Http\Controllers\CompanyAdmin\School\SchoolGradeController::class,'configure'])->middleware('module_permission:school.edit')->name('grades.configure')->middleware('school_permission:school.grades.manage');
Route::post('grades/evaluations', [\App\Http\Controllers\CompanyAdmin\School\SchoolGradeController::class,'storeEvaluation'])->middleware('module_permission:school.create')->name('grades.evaluations.store')->middleware('school_permission:school.grades.manage');
Route::get('grades/evaluations/{id}', [\App\Http\Controllers\CompanyAdmin\School\SchoolGradeController::class,'edit'])->whereNumber('id')->middleware('module_permission:school.view')->name('grades.evaluations.edit')->middleware('school_permission:school.grades.manage');
Route::put('grades/evaluations/{id}', [\App\Http\Controllers\CompanyAdmin\School\SchoolGradeController::class,'save'])->whereNumber('id')->middleware('module_permission:school.edit')->name('grades.evaluations.save')->middleware('school_permission:school.grades.manage');
Route::get('bulletins', [\App\Http\Controllers\CompanyAdmin\School\SchoolBulletinController::class,'index'])->middleware('module_permission:school.view')->name('bulletins.index');
Route::get('bulletins/{classId}/{studentId}', [\App\Http\Controllers\CompanyAdmin\School\SchoolBulletinController::class,'show'])->whereNumber('classId')->whereNumber('studentId')->middleware('module_permission:school.view')->name('bulletins.show');


/*
|--------------------------------------------------------------------------
| GESTION SCOLAIRE — FICHES VIERGES DE NOTES
|--------------------------------------------------------------------------
*/

Route::middleware('module_permission:school.view')
    ->prefix('grade-sheets')
    ->name('grade-sheets.')
    ->controller(
        \App\Http\Controllers\CompanyAdmin\School\SchoolGradeSheetController::class
    )
    ->group(function () {

        Route::get('/', 'index')
            ->name('index');

        Route::get('/print', 'print')
            ->name('print');

    });


/*
|--------------------------------------------------------------------------
| GESTION SCOLAIRE — CARTES SCOLAIRES
|--------------------------------------------------------------------------
*/

Route::prefix('cards')
    ->name('cards.')
    ->middleware('module_permission:school.view')
    ->controller(SchoolCardController::class)
    ->group(function () {

        Route::get('/', 'index')
            ->name('index');

        Route::get('/print', 'print')
            ->name('print');

    });


    Route::middleware('module_permission:school.view')->prefix('honors')->name('honors.')->group(function () {
    Route::get('/', [\App\Http\Controllers\CompanyAdmin\School\SchoolHonorController::class, 'index'])->name('index');
    Route::get('/print', [\App\Http\Controllers\CompanyAdmin\School\SchoolHonorController::class, 'print'])->name('print');
});
Route::middleware('module_permission:school.edit')->prefix('honors')->name('honors.')->group(function () {
    Route::get('/settings', [\App\Http\Controllers\CompanyAdmin\School\SchoolHonorController::class, 'settingsForm'])->name('settings');
    Route::put('/settings', [\App\Http\Controllers\CompanyAdmin\School\SchoolHonorController::class, 'updateSettings'])->name('settings.update');
});




/*
|--------------------------------------------------------------------------
| CONSEILS DE CLASSE
|--------------------------------------------------------------------------
*/

Route::prefix('councils')
    ->name('councils.')
    ->group(function () {

        // Affichage des conseils
        Route::get('/', [
            \App\Http\Controllers\CompanyAdmin\School\SchoolCouncilController::class,
            'index'
        ])
        ->middleware('module_permission:school.view')
        ->name('index');

        // Enregistrement des décisions (brouillon)
        Route::post('/', [
            \App\Http\Controllers\CompanyAdmin\School\SchoolCouncilController::class,
            'save'
        ])
        ->middleware('module_permission:school.edit')
        ->name('save');

        // Validation définitive
        Route::post('/validate', [
            \App\Http\Controllers\CompanyAdmin\School\SchoolCouncilController::class,
            'validateCouncil'
        ])
        ->middleware('module_permission:school.edit')
        ->name('validate');

        // Impression des procès-verbaux
        Route::get('/pv', [
            \App\Http\Controllers\CompanyAdmin\School\SchoolCouncilController::class,
            'pv'
        ])
        ->middleware('module_permission:school.view')
        ->name('pv');

    });


/*
|--------------------------------------------------------------------------
| GESTION SCOLAIRE — EMPLOIS DU TEMPS
|--------------------------------------------------------------------------
*/

Route::middleware('module_permission:school.view')
    ->prefix('timetables')
    ->name('timetables.')
    ->group(function () {

        Route::get('/', [
            \App\Http\Controllers\CompanyAdmin\School\SchoolTimetableController::class,
            'index'
        ])->name('index');

        Route::get('/print', [
            \App\Http\Controllers\CompanyAdmin\School\SchoolTimetableController::class,
            'print'
        ])->name('print');
    });

Route::middleware('module_permission:school.edit')
    ->prefix('timetables')
    ->name('timetables.')
    ->group(function () {

        Route::post('/', [
            \App\Http\Controllers\CompanyAdmin\School\SchoolTimetableController::class,
            'store'
        ])->name('store');

        Route::delete('/{id}', [
            \App\Http\Controllers\CompanyAdmin\School\SchoolTimetableController::class,
            'destroy'
        ])->whereNumber('id')->name('destroy');

        Route::post('/rooms', [
            \App\Http\Controllers\CompanyAdmin\School\SchoolTimetableController::class,
            'storeRoom'
        ])->name('rooms.store');
    });


/*
|--------------------------------------------------------------------------
| GESTION SCOLAIRE — BULLETINS ET RELEVÉS
|--------------------------------------------------------------------------
*/

Route::middleware('module_permission:school.view')
    ->prefix('reports')
    ->name('reports.')
    ->group(function () {

        Route::get('/', [
            \App\Http\Controllers\CompanyAdmin\School\SchoolBulletinController::class,
            'index'
        ])->name('index');

    });

    
/*
|--------------------------------------------------------------------------
| PARAMÈTRES DE L'ÉTABLISSEMENT SCOLAIRE
|--------------------------------------------------------------------------
*/

Route::prefix('settings')
    ->name('settings.')
    ->middleware('school_permission:school.settings.manage')
    ->controller(
        \App\Http\Controllers\CompanyAdmin\School\SchoolSettingController::class
    )
    ->group(function () {

        Route::get('/', 'index')
            ->middleware('module_permission:school.view')
            ->name('index');

        Route::put('/', 'update')
            ->middleware('module_permission:school.edit')
            ->name('update');

    });

    /*==================================================================
    * ABSENCES ET PRESENCES
    ==================================================================*/
    Route::get('/attendance', [SchoolAttendanceController::class, 'index'])
    ->middleware('school_permission:school.attendance.manage')
    ->name('attendance.index');
    Route::post('/attendance', [SchoolAttendanceController::class, 'store'])
    ->middleware('school_permission:school.attendance.manage')
    ->name('attendance.store');

    // Rapports d'assiduité : avant les éventuelles routes dynamiques.
    Route::get('/attendance/reports', [SchoolAttendanceReportController::class, 'index'])
        ->middleware('school_permission:school.attendance.manage')
        ->name('attendance.reports.index');
    Route::get('/attendance/reports/csv', [SchoolAttendanceReportController::class, 'exportCsv'])
        ->middleware('school_permission:school.attendance.manage')
        ->name('attendance.reports.csv');

    Route::get('/attendance/justifications', [SchoolAttendanceJustificationController::class, 'index'])
    ->middleware('school_permission:school.attendance.manage')->name('attendance.justifications.index');
    Route::post('/attendance/justifications/{id}', [SchoolAttendanceJustificationController::class, 'submit'])
    ->whereNumber('id')->middleware('school_permission:school.attendance.manage')->name('attendance.justifications.submit');
    Route::post('/attendance/justifications/{id}/review', [SchoolAttendanceJustificationController::class, 'review'])
    ->whereNumber('id')->middleware('school_permission:school.attendance.manage')->name('attendance.justifications.review');



/*
|--------------------------------------------------------------------------
| GESTION SCOLAIRE — UTILISATEURS
|--------------------------------------------------------------------------
*/

Route::prefix('users')
    ->name('users.')
    ->middleware('school_permission:school.users.manage')
    ->controller(SchoolUserController::class)
    ->group(function () {

        Route::get('/', 'index')
            ->name('index');

        Route::get('/create', 'create')
            ->name('create');

        Route::post('/', 'store')
            ->name('store');

        Route::get('/{user}/edit', 'edit')
            ->whereNumber('user')
            ->name('edit');

        Route::put('/{user}', 'update')
            ->whereNumber('user')
            ->name('update');

        Route::patch('/{user}/toggle', 'toggle')
            ->whereNumber('user')
            ->name('toggle');
    });

 //access utilisateur
    Route::prefix('access')->name('access.')
    ->middleware('school_permission:school.access.manage')->controller(SchoolAccessController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/{user}/reset-link', 'sendResetLink')->whereNumber('user')->middleware('throttle:5,1')->name('reset-link');

    Route::get('/export/excel', 'exportExcel')->name('export.excel');
Route::get('/export/pdf', 'exportPdf')->name('export.pdf');
Route::post('/{user}/sms', 'sendSms')->whereNumber('user')
    ->middleware('throttle:3,1')->name('sms');
});


Route::prefix('permissions')->name('permissions.')
    ->middleware('school_permission:school.permissions.manage')
    ->controller(SchoolPermissionController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{user}/edit', 'edit')->whereNumber('user')->name('edit');
        Route::put('/{user}', 'update')->whereNumber('user')->name('update');
    });
    



}); // Fin du groupe school

Route::middleware(['module:school', 'module_permission:school.edit'])
    ->prefix('school/teachers')
    ->name('school.teachers.')
    ->group(function () {
        Route::get('{teacher}/account', [\App\Http\Controllers\CompanyAdmin\School\TeacherAccountController::class, 'edit'])
            ->whereNumber('teacher')->name('account.edit');
        Route::post('{teacher}/account', [\App\Http\Controllers\CompanyAdmin\School\TeacherAccountController::class, 'store'])
            ->whereNumber('teacher')->name('account.store');
        Route::patch('{teacher}/account/toggle', [\App\Http\Controllers\CompanyAdmin\School\TeacherAccountController::class, 'toggle'])
            ->whereNumber('teacher')->name('account.toggle');
    });


    /*
|--------------------------------------------------------------------------
| NEXORA MÉDICAL — CLINIQUE
|--------------------------------------------------------------------------
*/

Route::middleware([
    'module:clinic',
    'module_permission:clinic.view',
])
->prefix('clinic')
->name('clinic.')
->group(function () {

    Route::get('/dashboard', [
        ClinicDashboardController::class,
        'index',
    ])->name('dashboard');

});


/*
|--------------------------------------------------------------------------
| NEXORA MÉDICAL — HÔPITAL
|--------------------------------------------------------------------------
*/

Route::middleware([
    'module:hospital',
    'module_permission:hospital.view',
])
->prefix('hospital')
->name('hospital.')
->group(function () {

    Route::get('/dashboard', [
        HospitalDashboardController::class,
        'index',
    ])->name('dashboard');

});

/*
|--------------------------------------------------------------------------
| NEXORA MÉDICAL — PATIENTS
|--------------------------------------------------------------------------
*/

Route::middleware(['medical_access'])
    ->prefix('medical')
    ->name('medical.')
    ->group(function () {

        Route::resource('patients', PatientController::class)
            ->except(['destroy']);

    });


    /** RENDEZ-VOUS */

    Route::middleware(['medical_access'])
    ->prefix('medical/appointments')
    ->name('medical.appointments.')
    ->group(function () {

        Route::get('/', [AppointmentController::class, 'index'])
            ->name('index');

        Route::get('/create', [AppointmentController::class, 'create'])
            ->name('create');

        Route::post('/', [AppointmentController::class, 'store'])
            ->name('store');

        Route::get('/{appointment}', [AppointmentController::class, 'show'])
            ->name('show');

        Route::patch('/{appointment}/status', [
            AppointmentController::class,
            'updateStatus'
        ])->name('status');
    });

    Route::middleware(['medical_access'])
    ->prefix('medical/practitioners')
    ->name('medical.practitioners.')
    ->group(function () {

        Route::get('/', [PractitionerController::class, 'index'])
            ->name('index');

        Route::get('/create', [PractitionerController::class, 'create'])
            ->name('create');

        Route::post('/', [PractitionerController::class, 'store'])
            ->name('store');

        Route::get('/{practitioner}/edit', [
            PractitionerController::class,
            'edit'
        ])->name('edit');

        Route::put('/{practitioner}', [
            PractitionerController::class,
            'update'
        ])->name('update');
    });

    Route::middleware(['medical_access'])
    ->prefix('medical/consultations')
    ->name('medical.consultations.')
    ->group(function () {

        Route::get('/', [ConsultationController::class, 'index'])
            ->name('index');

        Route::get('/create', [ConsultationController::class, 'create'])
            ->name('create');

        Route::post('/', [ConsultationController::class, 'store'])
            ->name('store');

        Route::get('/{consultation}', [
            ConsultationController::class,
            'show'
        ])->name('show');
    });

}); // Fin du groupe company

/*
|--------------------------------------------------------------------------
| ESPACE PROVISEUR — GROUPE DISTINCT DES ROUTES COMPANYADMIN
|--------------------------------------------------------------------------
| Ne pas placer sous le middleware companyadmin : le proviseur possède
| son propre middleware de contrôle d'accès.
*/

Route::middleware([
    'auth',
    \App\Http\Middleware\EnsureSchoolPrincipal::class,
    'module:school',
])
->prefix('company/school/principal')
->name('company.school.principal.')
->group(function () {

    Route::get('/dashboard', [
        SchoolPrincipalDashboardController::class,
        'index',
    ])->name('dashboard');

    Route::get('/classes', [
        SchoolPrincipalAcademicController::class,
        'classes',
    ])->name('classes.index');

    Route::get('/subjects', [
        SchoolPrincipalAcademicController::class,
        'subjects',
    ])->name('subjects.index');

    Route::get('/assignments', [
        SchoolPrincipalAcademicController::class,
        'assignments',
    ])->name('assignments.index');

    Route::get('/teachers', [
    SchoolPrincipalTeacherController::class,
    'index',
])->name('teachers.index');

Route::get('/teachers/{teacher}', [
    SchoolPrincipalTeacherController::class,
    'show',
])->whereNumber('teacher')->name('teachers.show');

Route::get('/results', [
    SchoolPrincipalResultsController::class,
    'index',
])->name('results.index');

Route::get('/results/by-class', [
    SchoolPrincipalResultsController::class,
    'byClass',
])->name('results.by-class');

Route::get('/results/ranking', [
    SchoolPrincipalRankingController::class,
    'index',
])->name('results.ranking');

Route::get('/bulletins', [
    SchoolPrincipalBulletinController::class,
    'index',
])->name('bulletins.index');

Route::get('/bulletins/{classId}/{studentId}', [
    SchoolPrincipalBulletinController::class,
    'show',
])
->whereNumber('classId')
->whereNumber('studentId')
->name('bulletins.show');

Route::get('/tuition', [
    SchoolPrincipalTuitionController::class,
    'index',
])->name('tuition.index');

});




/*
|--------------------------------------------------------------------------
| GESTION SCOLAIRE — VÉRIFICATION PUBLIQUE DES CARTES
|--------------------------------------------------------------------------
*/

Route::get(
    '/school/cards/verify/{companyId}/{yearId}/{studentId}',
    [
        \App\Http\Controllers\CompanyAdmin\School\SchoolCardController::class,
        'verify'
    ]
)
->whereNumber('companyId')
->whereNumber('yearId')
->whereNumber('studentId')
->middleware('signed')
->name('school.cards.verify');




/*
|--------------------------------------------------------------------------
| PROFIL
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->group(function () {


        Route::get(
            '/profile',
            [
                ProfileController::class,
                'edit',
            ]
        )->name('profile.edit');


        Route::patch(
            '/profile',
            [
                ProfileController::class,
                'update',
            ]
        )->name('profile.update');


        Route::delete(
            '/profile',
            [
                ProfileController::class,
                'destroy',
            ]
        )->name('profile.destroy');

    });


/*
|--------------------------------------------------------------------------
| WEBHOOKS PAIEMENTS
|--------------------------------------------------------------------------
|
| Ces routes restent publiques car elles sont appelées
| par les opérateurs de paiement.
|
*/

Route::post(
    '/webhooks/payments/orange-money',
    [
        PaymentWebhookController::class,
        'orange',
    ]
)->name('webhooks.payments.orange');


Route::post(
    '/webhooks/payments/mtn-momo',
    [
        PaymentWebhookController::class,
        'mtn',
    ]
)->name('webhooks.payments.mtn');


/*
|--------------------------------------------------------------------------
| PATH IMAGES / STORAGE
|--------------------------------------------------------------------------
*/

Route::get(
    '/storage/{path}',
    function ($path) {

        if (
            !Storage::disk('public')
                ->exists($path)
        ) {

            abort(404);

        }

        return Storage::disk('public')
            ->response($path);

    }
)->where(
    'path',
    '.*'
);


/*
|--------------------------------------------------------------------------
| AUTHENTIFICATION
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| BOUTIQUE PUBLIQUE — E-COMMERCE
|--------------------------------------------------------------------------
| Routes accessibles aux visiteurs sans connexion.
*/

Route::prefix('boutique/{company:slug}')
    ->name('storefront.')
    ->group(function () {

        // Accueil de la boutique
        Route::get('/', [
            StorefrontController::class,
            'index'
        ])->name('index');

        // Liste des collections
        Route::get('/collections', [
            StorefrontController::class,
            'collections'
        ])->name('collections.index');

        // Détail d'une collection
        Route::get('/collections/{slug}', [
            StorefrontController::class,
            'showCollection'
        ])->name('collections.show');

    });

    Route::prefix('boutique/{company:slug}')
    ->name('storefront.')
    ->group(function () {

        Route::get(
            '/produits',
            [StorefrontProductController::class, 'index']
        )->name('products.index');

        Route::get(
            '/produits/{slug}',
            [StorefrontProductController::class, 'show']
        )->name('products.show');

        
Route::post('/produits/{slug}/avis', [
    StorefrontReviewController::class,
    'store'
])
->middleware('throttle:5,10')
->name('reviews.store');


    });

    
/*
|--------------------------------------------------------------------------
| BOUTIQUE PUBLIQUE — PANIER ET CHECKOUT
|--------------------------------------------------------------------------
*/

Route::prefix('boutique/{company:slug}')
    ->name('storefront.')
    ->group(function () {

        // Afficher le panier
        Route::get('/panier', [
            StorefrontCartController::class,
            'index'
        ])->name('cart.index');

        // Ajouter un produit
        Route::post('/panier/ajouter', [
            StorefrontCartController::class,
            'store'
        ])->name('cart.store');

        // Modifier la quantité
        Route::patch('/panier/{productId}', [
            StorefrontCartController::class,
            'update'
        ])->whereNumber('productId')
          ->name('cart.update');

        // Supprimer un produit
        Route::delete('/panier/{productId}', [
            StorefrontCartController::class,
            'destroy'
        ])->whereNumber('productId')
          ->name('cart.destroy');

        // Vider le panier
        Route::delete('/panier', [
            StorefrontCartController::class,
            'clear'
        ])->name('cart.clear');

    });

    
/*
|--------------------------------------------------------------------------
| BOUTIQUE PUBLIQUE — COMMANDES
|--------------------------------------------------------------------------
*/

Route::prefix('boutique/{company:slug}')
    ->name('storefront.')
    ->group(function () {

        // Formulaire de commande
        Route::get('/commande', [
            StorefrontCheckoutController::class,
            'index'
        ])->name('checkout.index');

        // Confirmer la commande
        Route::post('/commande', [
            StorefrontCheckoutController::class,
            'store'
        ])->name('checkout.store');

        // Confirmation après enregistrement
        Route::get('/commande/confirmation/{reference}', [
            StorefrontCheckoutController::class,
            'success'
        ])->name('checkout.success');

    });

    
/*
|--------------------------------------------------------------------------
| SITE WEB PUBLIC DES ENTREPRISES
|--------------------------------------------------------------------------
*/

Route::prefix('site/{company:slug}')
    ->name('website.public.')
    ->group(function () {

        Route::get('/', [
            WebsitePublicController::class,
            'index'
        ])->name('index');

        Route::get('/{slug}', [
            WebsitePublicController::class,
            'show'
        ])->name('show');


    });




require __DIR__ . '/auth.php';
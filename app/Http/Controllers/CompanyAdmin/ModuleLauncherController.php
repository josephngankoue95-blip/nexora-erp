<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Services\CompanyModuleService;
use Illuminate\Http\Request;

class ModuleLauncherController extends Controller
{
    public function index(
        Request $request,
        CompanyModuleService $companyModuleService
    ) {
        $user = $request->user();
        $company = $user->company;

        if (!$company) {
            abort(403, 'Aucune entreprise associée à votre compte.');
        }

        $activeModules = $companyModuleService
            ->getActiveModuleSlugs($company);

        /*
        |--------------------------------------------------------------------------
        | ESPACES DE TRAVAIL
        |--------------------------------------------------------------------------
        |
        | Un workspace peut regrouper plusieurs modules ERP.
        |
        */

        $workspaces = [

            /*
            |--------------------------------------------------------------------------
            | GESTION DE STOCK / ERP
            |--------------------------------------------------------------------------
            */

            'stock' => [
                'name' => 'Gestion de stock',
                'short_name' => 'ERP',
                'description' =>
                    'Gérez vos produits, stocks, ventes, achats, clients et finances.',
                'icon' => '📦',
                'color' => '#315bea',
                'route' => 'company.dashboard',

                'modules' => [
                    'products',
                    'categories',
                    'stock',
                    'services',
                    'clients',
                    'suppliers',
                    'sales',
                    'purchases',
                    'payments',
                    'finance',
                    'reports',
                ],

                'permissions' => [
                    'products.view',
                    'categories.view',
                    'stock.view',
                    'services.view',
                    'clients.view',
                    'suppliers.view',
                    'sales.view',
                    'purchases.view',
                    'payments.view',
                    'finance.view',
                    'reports.view',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | E-COMMERCE
            |--------------------------------------------------------------------------
            */

            'ecommerce' => [
                'name' => 'E-commerce',
                'short_name' => 'Boutique en ligne',
                'description' =>
                    'Gérez votre catalogue, votre boutique et vos futures commandes en ligne.',
                'icon' => '🛍️',
                'color' => '#7c3aed',
                'route' => 'company.ecommerce.index',

                'modules' => [
                    'ecommerce',
                ],

                'permissions' => [
                    'ecommerce.view',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | HÔTEL
            |--------------------------------------------------------------------------
            */

            'hotel' => [
                'name' => 'Gestion hôtelière',
                'short_name' => 'Hôtel',
                'description' =>
                    'Chambres, réservations, clients, facturation et services hôteliers.',
                'icon' => '🏨',
                'color' => '#0891b2',
                'route' => null,

                'modules' => [
                    'hotel',
                ],

                'permissions' => [
                    'hotel.view',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | RESTAURANT
            |--------------------------------------------------------------------------
            */

            'restaurant' => [
                'name' => 'Gestion restaurant',
                'short_name' => 'Restaurant',
                'description' =>
                    'Tables, commandes, cuisine, caisse et gestion du restaurant.',
                'icon' => '🍽️',
                'color' => '#ea580c',
                'route' => null,

                'modules' => [
                    'restaurant',
                ],

                'permissions' => [
                    'restaurant.view',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | ÉTABLISSEMENT SCOLAIRE
            |--------------------------------------------------------------------------
            */

            'school' => [
                'name' => 'Gestion scolaire',
                'short_name' => 'École',
                'description' =>
                    'Élèves, classes, enseignants, scolarité, notes et bulletins.',
                'icon' => '🏫',
                'color' => '#16a34a',
                'route' => 'company.school.dashboard',

                'modules' => [
                    'school',
                ],

                'permissions' => [
                    'school.view',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | CLINIQUE
            |--------------------------------------------------------------------------
            */

            'clinic' => [
                'name' => 'Gestion clinique',
                'short_name' => 'Clinique',
                'description' =>
                    'Patients, rendez-vous, consultations et suivi médical.',
                'icon' => '🩺',
                'color' => '#dc2626',
                'route' => 'company.clinic.dashboard',

                'modules' => [
                    'clinic',
                ],

                'permissions' => [
                    'clinic.view',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | HÔPITAL
            |--------------------------------------------------------------------------
            */

            'hospital' => [
                'name' => 'Gestion hospitalière',
                'short_name' => 'Hôpital',
                'description' =>
                    'Patients, services, hospitalisations et gestion hospitalière.',
                'icon' => '🏥',
                'color' => '#be123c',
               'route' => 'company.hospital.dashboard',

                'modules' => [
                    'hospital',
                ],

                'permissions' => [
                    'hospital.view',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | PHARMACIE
            |--------------------------------------------------------------------------
            */

            'pharmacy' => [
                'name' => 'Gestion pharmacie',
                'short_name' => 'Pharmacie',
                'description' =>
                    'Produits pharmaceutiques, stock, ventes et gestion des clients.',
                'icon' => '💊',
                'color' => '#059669',
                'route' => null,

                'modules' => [
                    'pharmacy',
                ],

                'permissions' => [
                    'pharmacy.view',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | LOCATION DE VÉHICULES
            |--------------------------------------------------------------------------
            */

            'vehicle-rental' => [
                'name' => 'Location de véhicules',
                'short_name' => 'Véhicules',
                'description' =>
                    'Véhicules, contrats, clients, locations et paiements.',
                'icon' => '🚗',
                'color' => '#2563eb',
                'route' => null,

                'modules' => [
                    'vehicle-rental',
                ],

                'permissions' => [
                    'vehicle-rental.view',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | IMMOBILIER
            |--------------------------------------------------------------------------
            */

            'real-estate' => [
                'name' => 'Gestion immobilière',
                'short_name' => 'Immobilier',
                'description' =>
                    'Biens, propriétaires, locataires, locations et paiements.',
                'icon' => '🏢',
                'color' => '#9333ea',
                'route' => null,

                'modules' => [
                    'real-estate',
                ],

                'permissions' => [
                    'real-estate.view',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | RH
            |--------------------------------------------------------------------------
            */

            'hr' => [
                'name' => 'Ressources humaines',
                'short_name' => 'RH',
                'description' =>
                    'Employés, contrats, congés, absences et gestion du personnel.',
                'icon' => '👥',
                'color' => '#4f46e5',
                'route' => null,

                'modules' => [
                    'hr',
                ],

                'permissions' => [
                    'hr.view',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | PROJETS
            |--------------------------------------------------------------------------
            */

            'projects' => [
                'name' => 'Gestion de projets',
                'short_name' => 'Projets',
                'description' =>
                    'Projets, tâches, équipes, échéances et suivi.',
                'icon' => '📊',
                'color' => '#0f766e',
                'route' => null,

                'modules' => [
                    'projects',
                ],

                'permissions' => [
                    'projects.view',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | MAINTENANCE
            |--------------------------------------------------------------------------
            */

            'maintenance' => [
                'name' => 'Maintenance',
                'short_name' => 'Maintenance',
                'description' =>
                    'Équipements, interventions, techniciens et maintenance.',
                'icon' => '🛠️',
                'color' => '#ca8a04',
                'route' => null,

                'modules' => [
                    'maintenance',
                ],

                'permissions' => [
                    'maintenance.view',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | DOCUMENTS
            |--------------------------------------------------------------------------
            */

            'documents' => [
                'name' => 'Gestion documentaire',
                'short_name' => 'Documents',
                'description' =>
                    'Classez, stockez et gérez vos documents professionnels.',
                'icon' => '📁',
                'color' => '#475569',
                'route' => null,

                'modules' => [
                    'documents',
                ],

                'permissions' => [
                    'documents.view',
                ],
            ],

            
            /*
            |--------------------------------------------------------------------------
            | SITE WEB
            |--------------------------------------------------------------------------
            */

            'website' => [
                'name' => 'Site Web',
                'short_name' => 'Site Web',
                'description' => 'Créez vos pages, menus, actualités et personnalisez votre site.',
                'icon' => '🌐',
                'color' => '#315bea',
                'route' => 'company.website.index',

                'modules' => [
                    'website',
                ],

                'permissions' => [
                    'website.view',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | E-LEARNING
            |--------------------------------------------------------------------------
            */

            'elearning' => [
                'name' => 'E-Learning',
                'short_name' => 'Formation',
                'description' => 'Gérez les cours, les apprenants et les évaluations.',
                'icon' => '🎓',
                'color' => '#7c3aed',
                'route' => null,

                'modules' => [
                    'elearning',
                ],

                'permissions' => [
                    'elearning.view',
                ],
            ],

        ];

        /*
        |--------------------------------------------------------------------------
        | FILTRAGE
        |--------------------------------------------------------------------------
        */

        $availableWorkspaces = [];

        foreach ($workspaces as $slug => $workspace) {

            /*
            | Un workspace est disponible si au moins un de ses
            | modules est activé pour l'entreprise.
            */

            $hasActiveModule = false;

            foreach ($workspace['modules'] as $module) {

                if (
                    in_array(
                        $module,
                        $activeModules,
                        true
                    )
                ) {
                    $hasActiveModule = true;
                    break;
                }
            }

            if (!$hasActiveModule) {
                continue;
            }

            /*
            | Super Admin
            */

            if ($user->isSuperAdmin()) {
                $workspace['has_access'] = true;
                $availableWorkspaces[$slug] = $workspace;
                continue;
            }

            /*
            | Vérification des permissions.
            |
            | On utilise volontairement le système existant
            | hasPermission(), et non Laravel can().
            */

            $hasPermission = false;

            foreach ($workspace['permissions'] as $permission) {

                if ($user->hasPermission($permission)) {
                    $hasPermission = true;
                    break;
                }
            }

            if (!$hasPermission) {
                continue;
            }

            $workspace['has_access'] = true;

            $availableWorkspaces[$slug] = $workspace;
        }

        return view(
            'company.modules.index',
            compact(
                'availableWorkspaces',
                'company',
                'user'
            )
        );
    }
}
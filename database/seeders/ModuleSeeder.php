<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            /*
            |--------------------------------------------------------------------------
            | MODULE PARENT : GESTION DE STOCK
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Gestion de stock',
                'slug' => 'inventory',
                'description' => 'Gestion centralisée des produits, stocks, achats, ventes et prestations.',
                'icon' => '📦',
                'sort_order' => 5,
            ],

            /*
            |--------------------------------------------------------------------------
            | MODULES INTERNES DE GESTION DE STOCK
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Produits',
                'slug' => 'products',
                'description' => 'Gestion des produits et articles.',
                'icon' => '📦',
                'sort_order' => 10,
            ],
            [
                'name' => 'Catégories',
                'slug' => 'categories',
                'description' => 'Gestion des catégories de produits.',
                'icon' => '🏷️',
                'sort_order' => 20,
            ],
            [
                'name' => 'Stock',
                'slug' => 'stock',
                'description' => 'Gestion des stocks et mouvements.',
                'icon' => '🏬',
                'sort_order' => 30,
            ],
            [
                'name' => 'Clients',
                'slug' => 'clients',
                'description' => 'Gestion des clients.',
                'icon' => '👥',
                'sort_order' => 40,
            ],
            [
                'name' => 'Fournisseurs',
                'slug' => 'suppliers',
                'description' => 'Gestion des fournisseurs.',
                'icon' => '🚚',
                'sort_order' => 50,
            ],
            [
                'name' => 'Ventes',
                'slug' => 'sales',
                'description' => 'Gestion des ventes.',
                'icon' => '🛒',
                'sort_order' => 60,
            ],
            [
                'name' => 'Achats',
                'slug' => 'purchases',
                'description' => 'Gestion des achats.',
                'icon' => '🧾',
                'sort_order' => 70,
            ],
            [
                'name' => 'Paiements',
                'slug' => 'payments',
                'description' => 'Gestion des paiements.',
                'icon' => '💳',
                'sort_order' => 80,
            ],
            [
                'name' => 'Finances',
                'slug' => 'finance',
                'description' => 'Gestion financière de l’entreprise.',
                'icon' => '💰',
                'sort_order' => 90,
            ],
            [
                'name' => 'Rapports',
                'slug' => 'reports',
                'description' => 'Rapports et statistiques.',
                'icon' => '📊',
                'sort_order' => 100,
            ],
            [
                'name' => 'Prestations',
                'slug' => 'services',
                'description' => 'Gestion des services et prestations.',
                'icon' => '🔧',
                'sort_order' => 110,
            ],

            /*
            |--------------------------------------------------------------------------
            | AUTRES MODULES MÉTIERS
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Hôtel',
                'slug' => 'hotel',
                'description' => 'Gestion hôtelière.',
                'icon' => '🏨',
                'sort_order' => 200,
            ],
            [
                'name' => 'Restaurant',
                'slug' => 'restaurant',
                'description' => 'Gestion de restaurant.',
                'icon' => '🍽️',
                'sort_order' => 210,
            ],
            [
                'name' => 'Établissement scolaire',
                'slug' => 'school',
                'description' => 'Gestion d’établissement scolaire.',
                'icon' => '🏫',
                'sort_order' => 220,
            ],
            [
                'name' => 'Clinique',
                'slug' => 'clinic',
                'description' => 'Gestion de clinique.',
                'icon' => '🩺',
                'sort_order' => 230,
            ],
            [
                'name' => 'Hôpital',
                'slug' => 'hospital',
                'description' => 'Gestion hospitalière.',
                'icon' => '🏥',
                'sort_order' => 240,
            ],
            [
                'name' => 'Pharmacie',
                'slug' => 'pharmacy',
                'description' => 'Gestion de pharmacie.',
                'icon' => '💊',
                'sort_order' => 250,
            ],
            [
                'name' => 'E-commerce',
                'slug' => 'ecommerce',
                'description' => 'Boutique en ligne et commerce électronique.',
                'icon' => '🌐',
                'sort_order' => 260,
            ],
            [
                'name' => 'Location de véhicules',
                'slug' => 'vehicle-rental',
                'description' => 'Gestion de location de véhicules.',
                'icon' => '🚗',
                'sort_order' => 270,
            ],
            [
                'name' => 'Immobilier',
                'slug' => 'real-estate',
                'description' => 'Gestion immobilière.',
                'icon' => '🏠',
                'sort_order' => 280,
            ],
            [
                'name' => 'Ressources humaines',
                'slug' => 'hr',
                'description' => 'Gestion des ressources humaines.',
                'icon' => '👔',
                'sort_order' => 290,
            ],
            [
                'name' => 'Projets',
                'slug' => 'projects',
                'description' => 'Gestion des projets.',
                'icon' => '📋',
                'sort_order' => 300,
            ],
            [
                'name' => 'Maintenance',
                'slug' => 'maintenance',
                'description' => 'Gestion de maintenance.',
                'icon' => '🛠️',
                'sort_order' => 310,
            ],
            [
                'name' => 'Documents',
                'slug' => 'documents',
                'description' => 'Gestion documentaire.',
                'icon' => '📁',
                'sort_order' => 320,
            ],
            [
                'name' => 'Site Web',
                'slug' => 'website',
                'description' => 'Création et gestion du site web de l’entreprise.',
                'icon' => '🌐',
                'sort_order' => 330,
            ],
            [
                'name' => 'E-Learning',
                'slug' => 'elearning',
                'description' => 'Gestion des formations, cours, apprenants et évaluations.',
                'icon' => '🎓',
                'sort_order' => 340,
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | CRÉATION OU MISE À JOUR DES MODULES
        |--------------------------------------------------------------------------
        */
        foreach ($modules as $moduleData) {
            Module::updateOrCreate(
                ['slug' => $moduleData['slug']],
                [
                    'name' => $moduleData['name'],
                    'description' => $moduleData['description'],
                    'icon' => $moduleData['icon'],
                    'is_active' => true,
                    'sort_order' => $moduleData['sort_order'],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MODULES INTERNES DE GESTION DE STOCK
        |--------------------------------------------------------------------------
        |
        | Cette liste permet de conserver les anciens slugs utilisés par
        | les routes, contrôleurs, menus et vérifications de permissions.
        |
        */
        $stockSlugs = [
            'products',
            'categories',
            'stock',
            'clients',
            'suppliers',
            'sales',
            'purchases',
            'payments',
            'finance',
            'reports',
            'services',
            // 'website',
        ];

        $stockModules = Module::query()
            ->whereIn('slug', array_merge(['inventory'], $stockSlugs))
            ->get();

        /*
        |--------------------------------------------------------------------------
        | ENTREPRISES EXISTANTES
        |--------------------------------------------------------------------------
        |
        | Ce comportement active le module de stock et ses sous-modules
        | pour toutes les entreprises existantes, comme dans le seeder
        | initial. Les autres modules métiers ne sont pas activés ici.
        |
        */
        $companies = Company::query()->get();

        foreach ($companies as $company) {
            foreach ($stockModules as $module) {
                DB::table('company_modules')->updateOrInsert(
                    [
                        'company_id' => $company->id,
                        'module_id' => $module->id,
                    ],
                    [
                        'is_active' => true,
                        'activated_at' => now(),
                        'deactivated_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
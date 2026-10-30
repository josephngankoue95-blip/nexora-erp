<?php

namespace App\Support;

class ModuleRegistry
{
    /**
     * Liste centrale des modules ERP.
     */
    public static function all(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | COMMERCE / GESTION
            |--------------------------------------------------------------------------
            */

            /*
            |--------------------------------------------------------------------------
            | GESTION DE STOCK
            |--------------------------------------------------------------------------
            */

            'inventory' => [
                'name' => 'Gestion de stock',
                'slug' => 'inventory',
                'icon' => '📦',
                'description' => 'Gestion centralisée des articles, catégories, stocks, fournisseurs, achats, ventes et finances.',
                'sort_order' => 10,

                'permissions' => [
                    // Produits et catégories
                    'products.view',
                    'products.create',
                    'products.edit',
                    'products.delete',

                    //Services
                    'services.view',
                    'services.create',
                    'services.edit',
                    'services.delete',

                    'categories.view',
                    'categories.create',
                    'categories.edit',
                    'categories.delete',

                    // Stock
                    'stock.view',
                    'stock.entry',
                    'stock.exit',
                    'stock.adjust',

                    // Clients et fournisseurs
                    'clients.view',
                    'clients.create',
                    'clients.edit',
                    'clients.delete',

                    'suppliers.view',
                    'suppliers.create',
                    'suppliers.edit',
                    'suppliers.delete',

                    // Ventes et achats
                    'sales.view',
                    'sales.create',
                    'sales.cancel',

                    'purchases.view',
                    'purchases.create',
                    'purchases.cancel',

                    // Paiements et finances
                    'payments.view',
                    'payments.create',
                    'payments.cancel',

                    'finance.view',

                    // Rapports
                    'reports.view',
                    'reports.export',
                    ''
                ],

                'route' => 'company.stock.index',
            ],


            // 'products' => [
            //     'name' => 'Produits',
            //     'slug' => 'products',
            //     'icon' => '📦',
            //     'description' => 'Gestion des produits et articles.',
            //     'sort_order' => 10,
            //     'permissions' => [
            //         'products.view',
            //         'products.create',
            //         'products.edit',
            //         'products.delete',
            //     ],
            //     'route' => 'company.products.index',
            // ],

            // 'categories' => [
            //     'name' => 'Catégories',
            //     'slug' => 'categories',
            //     'icon' => '🗂️',
            //     'description' => 'Organisation des produits par catégories.',
            //     'sort_order' => 20,
            //     'permissions' => [
            //         'categories.view',
            //         'categories.create',
            //         'categories.edit',
            //         'categories.delete',
            //     ],
            //     'route' => 'company.categories.index',
            // ],

            // 'stock' => [
            //     'name' => 'Stock',
            //     'slug' => 'stock',
            //     'icon' => '🏬',
            //     'description' => 'Gestion des stocks, entrées et sorties.',
            //     'sort_order' => 30,
            //     'permissions' => [
            //         'stock.view',
            //         'stock.entry',
            //         'stock.exit',
            //         'stock.adjust',
            //     ],
            //     'route' => 'company.stock.index',
            // ],

            // 'clients' => [
            //     'name' => 'Clients',
            //     'slug' => 'clients',
            //     'icon' => '👥',
            //     'description' => 'Gestion des clients et contacts.',
            //     'sort_order' => 40,
            //     'permissions' => [
            //         'clients.view',
            //         'clients.create',
            //         'clients.edit',
            //         'clients.delete',
            //     ],
            //     'route' => 'company.clients.index',
            // ],

            // 'suppliers' => [
            //     'name' => 'Fournisseurs',
            //     'slug' => 'suppliers',
            //     'icon' => '🚚',
            //     'description' => 'Gestion des fournisseurs.',
            //     'sort_order' => 50,
            //     'permissions' => [
            //         'suppliers.view',
            //         'suppliers.create',
            //         'suppliers.edit',
            //         'suppliers.delete',
            //     ],
            //     'route' => 'company.suppliers.index',
            // ],

            // 'sales' => [
            //     'name' => 'Ventes',
            //     'slug' => 'sales',
            //     'icon' => '🛒',
            //     'description' => 'Gestion des ventes et commandes clients.',
            //     'sort_order' => 60,
            //     'permissions' => [
            //         'sales.view',
            //         'sales.create',
            //         'sales.cancel',
            //     ],
            //     'route' => 'company.sales.index',
            // ],

            // 'purchases' => [
            //     'name' => 'Achats',
            //     'slug' => 'purchases',
            //     'icon' => '🛍️',
            //     'description' => 'Gestion des achats et approvisionnements.',
            //     'sort_order' => 70,
            //     'permissions' => [
            //         'purchases.view',
            //         'purchases.create',
            //         'purchases.cancel',
            //     ],
            //     'route' => 'company.purchases.index',
            // ],

            // 'payments' => [
            //     'name' => 'Paiements',
            //     'slug' => 'payments',
            //     'icon' => '💳',
            //     'description' => 'Gestion des paiements et moyens de paiement.',
            //     'sort_order' => 80,
            //     'permissions' => [
            //         'payments.view',
            //         'payments.create',
            //         'payments.cancel',
            //     ],
            //     'route' => 'company.settings.payments',
            // ],

            // 'finance' => [
            //     'name' => 'Finances',
            //     'slug' => 'finance',
            //     'icon' => '💰',
            //     'description' => 'Suivi financier, encaissements et créances.',
            //     'sort_order' => 90,
            //     'permissions' => [
            //         'finance.view',
            //     ],
            //     'route' => 'company.finance.index',
            // ],

            // 'reports' => [
            //     'name' => 'Rapports',
            //     'slug' => 'reports',
            //     'icon' => '📊',
            //     'description' => 'Statistiques, analyses et rapports.',
            //     'sort_order' => 100,
            //     'permissions' => [
            //         'reports.view',
            //         'reports.export',
            //     ],
            //     'route' => 'company.reports.index',
            // ],

            // 'services' => [
            //     'name' => 'Prestations',
            //     'slug' => 'services',
            //     'icon' => '🔧',
            //     'description' => 'Gestion des prestations et services.',
            //     'sort_order' => 110,
            //     'permissions' => [
            //         'services.view',
            //         'services.create',
            //         'services.edit',
            //         'services.delete',
            //     ],
            //     'route' => 'company.services.index',
            // ],



            /*
            |--------------------------------------------------------------------------
            | HÔTELLERIE / RESTAURATION
            |--------------------------------------------------------------------------
            */

            'hotel' => [
                'name' => 'Hôtel',
                'slug' => 'hotel',
                'icon' => '🏨',
                'description' => 'Gestion complète d’un établissement hôtelier.',
                'sort_order' => 200,
                'permissions' => [
                    'hotel.view',
                    'hotel.create',
                    'hotel.edit',
                    'hotel.delete',
                ],
                'route' => null,
            ],

            'restaurant' => [
                'name' => 'Restaurant',
                'slug' => 'restaurant',
                'icon' => '🍽️',
                'description' => 'Gestion des tables, commandes et restauration.',
                'sort_order' => 210,
                'permissions' => [
                    'restaurant.view',
                    'restaurant.create',
                    'restaurant.edit',
                    'restaurant.delete',
                ],
                'route' => null,
            ],

            /*
            |--------------------------------------------------------------------------
            | SANTÉ
            |--------------------------------------------------------------------------
            */

            'clinic' => [
                'name' => 'Clinique',
                'slug' => 'clinic',
                'icon' => '🏥',
                'description' => 'Gestion d’une clinique.',
                'sort_order' => 300,
                'permissions' => [
                    'clinic.view',
                    'clinic.create',
                    'clinic.edit',
                    'clinic.delete',
                ],
                'route' => 'company.clinic.dashboard',
            ],

            'hospital' => [
                'name' => 'Hôpital',
                'slug' => 'hospital',
                'icon' => '🏨',
                'description' => 'Gestion d’un établissement hospitalier.',
                'sort_order' => 310,
                'permissions' => [
                    'hospital.view',
                    'hospital.create',
                    'hospital.edit',
                    'hospital.delete',
                ],
                'route' => 'company.hospital.dashboard',
            ],

            'pharmacy' => [
                'name' => 'Pharmacie',
                'slug' => 'pharmacy',
                'icon' => '💊',
                'description' => 'Gestion d’une pharmacie.',
                'sort_order' => 320,
                'permissions' => [
                    'pharmacy.view',
                    'pharmacy.create',
                    'pharmacy.edit',
                    'pharmacy.delete',
                ],
                'route' => null,
            ],

            /*
            |--------------------------------------------------------------------------
            | ÉDUCATION
            |--------------------------------------------------------------------------
            */

            'school' => [
                'name' => 'Établissement scolaire',
                'slug' => 'school',
                'icon' => '🎓',
                'description' => 'Gestion d’une école, collège ou lycée.',
                'sort_order' => 400,
                'permissions' => [
                    'school.view',
                    'school.create',
                    'school.edit',
                    'school.delete',
                ],
                'route' => 'company.school.dashboard',
            ],

            /*
            |--------------------------------------------------------------------------
            | DIGITAL / COMMERCE
            |--------------------------------------------------------------------------
            */

            'ecommerce' => [
                    'name' => 'E-commerce',

                    'permissions' => [
                        'ecommerce.view',
                        'ecommerce.create',
                        'ecommerce.edit',
                        'ecommerce.delete',
                    ],

                    'route' => 'company.ecommerce.index',

                    'icon' => 'shopping-bag',

                    'sort_order' => 260,
                ],

                
            /*
            |--------------------------------------------------------------------------
            | SITE WEB
            |--------------------------------------------------------------------------
            */

            'website' => [
                'name' => 'Site Web',
                'slug' => 'website',
                'icon' => '🌐',
                'description' => 'Création et gestion du site web de l’entreprise.',
                'sort_order' => 330,

                'permissions' => [
                    'website.view',
                    'website.create',
                    'website.edit',
                    'website.delete',
                    'website.publish',
                ],

                'route' => 'company.website.index',
            ],

            /*
            |--------------------------------------------------------------------------
            | LOCATION
            |--------------------------------------------------------------------------
            */

            'vehicle-rental' => [
                'name' => 'Location de véhicules',
                'slug' => 'vehicle-rental',
                'icon' => '🚗',
                'description' => 'Gestion des véhicules et locations.',
                'sort_order' => 600,
                'permissions' => [
                    'vehicle-rental.view',
                    'vehicle-rental.create',
                    'vehicle-rental.edit',
                    'vehicle-rental.delete',
                ],
                'route' => null,
            ],

            /*
            |--------------------------------------------------------------------------
            | IMMOBILIER
            |--------------------------------------------------------------------------
            */

            'real-estate' => [
                'name' => 'Immobilier',
                'slug' => 'real-estate',
                'icon' => '🏠',
                'description' => 'Gestion des biens immobiliers et locations.',
                'sort_order' => 700,
                'permissions' => [
                    'real-estate.view',
                    'real-estate.create',
                    'real-estate.edit',
                    'real-estate.delete',
                ],
                'route' => null,
            ],

            /*
            |--------------------------------------------------------------------------
            | RESSOURCES HUMAINES
            |--------------------------------------------------------------------------
            */

            'hr' => [
                'name' => 'Ressources humaines',
                'slug' => 'hr',
                'icon' => '👨‍💼',
                'description' => 'Gestion des employés et ressources humaines.',
                'sort_order' => 800,
                'permissions' => [
                    'hr.view',
                    'hr.create',
                    'hr.edit',
                    'hr.delete',
                ],
                'route' => null,
            ],

            /*
            |--------------------------------------------------------------------------
            | PROJETS
            |--------------------------------------------------------------------------
            */

            'projects' => [
                'name' => 'Projets',
                'slug' => 'projects',
                'icon' => '📋',
                'description' => 'Gestion des projets et tâches.',
                'sort_order' => 900,
                'permissions' => [
                    'projects.view',
                    'projects.create',
                    'projects.edit',
                    'projects.delete',
                ],
                'route' => null,
            ],

            /*
            |--------------------------------------------------------------------------
            | MAINTENANCE
            |--------------------------------------------------------------------------
            */

            'maintenance' => [
                'name' => 'Maintenance',
                'slug' => 'maintenance',
                'icon' => '🛠️',
                'description' => 'Gestion des interventions et maintenance.',
                'sort_order' => 1000,
                'permissions' => [
                    'maintenance.view',
                    'maintenance.create',
                    'maintenance.edit',
                    'maintenance.delete',
                ],
                'route' => null,
            ],

            /*
            |--------------------------------------------------------------------------
            | DOCUMENTS
            |--------------------------------------------------------------------------
            */

            'documents' => [
                'name' => 'Documents',
                'slug' => 'documents',
                'icon' => '📁',
                'description' => 'Gestion centralisée des documents.',
                'sort_order' => 1100,
                'permissions' => [
                    'documents.view',
                    'documents.create',
                    'documents.edit',
                    'documents.delete',
                ],
                'route' => null,
            ],
        ];
    }

    /**
     * Récupérer un module par son slug.
     */
    public static function get(string $slug): ?array
    {
        return self::all()[$slug] ?? null;
    }

    /**
     * Vérifier si un module existe.
     */
    public static function exists(string $slug): bool
    {
        return isset(self::all()[$slug]);
    }

    /**
     * Récupérer uniquement les slugs.
     */
    public static function slugs(): array
    {
        return array_keys(self::all());
    }

    /**
     * Récupérer les permissions d'un module.
     */
    public static function permissions(string $slug): array
    {
        return self::get($slug)['permissions'] ?? [];
    }

    /**
     * Récupérer les modules ayant une route.
     */
    public static function navigable(): array
    {
        return array_filter(
            self::all(),
            fn (array $module) => !empty($module['route'])
        );
    }

    /**
     * Récupérer les modules triés.
     */
    public static function sorted(): array
    {
        $modules = self::all();

        uasort(
            $modules,
            fn (array $a, array $b) =>
                $a['sort_order'] <=> $b['sort_order']
        );

        return $modules;
    }
}
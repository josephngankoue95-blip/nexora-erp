<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            /*
            |--------------------------------------------------------------------------
            | PRODUITS
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'products.view',
                'label' => 'Voir les produits',
                'module' => 'products',
            ],
            [
                'name' => 'products.create',
                'label' => 'Créer des produits',
                'module' => 'products',
            ],
            [
                'name' => 'products.edit',
                'label' => 'Modifier les produits',
                'module' => 'products',
            ],
            [
                'name' => 'products.delete',
                'label' => 'Supprimer les produits',
                'module' => 'products',
            ],

            /*
            |--------------------------------------------------------------------------
            | CATÉGORIES
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'categories.view',
                'label' => 'Voir les catégories',
                'module' => 'categories',
            ],
            [
                'name' => 'categories.create',
                'label' => 'Créer des catégories',
                'module' => 'categories',
            ],
            [
                'name' => 'categories.edit',
                'label' => 'Modifier les catégories',
                'module' => 'categories',
            ],
            [
                'name' => 'categories.delete',
                'label' => 'Supprimer les catégories',
                'module' => 'categories',
            ],

            /*
            |--------------------------------------------------------------------------
            | STOCK
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'stock.view',
                'label' => 'Voir le stock',
                'module' => 'stock',
            ],
            [
                'name' => 'stock.entry',
                'label' => 'Faire une entrée de stock',
                'module' => 'stock',
            ],
            [
                'name' => 'stock.exit',
                'label' => 'Faire une sortie de stock',
                'module' => 'stock',
            ],
            [
                'name' => 'stock.adjust',
                'label' => 'Ajuster le stock',
                'module' => 'stock',
            ],

            /*
            |--------------------------------------------------------------------------
            | CLIENTS
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'clients.view',
                'label' => 'Voir les clients',
                'module' => 'clients',
            ],
            [
                'name' => 'clients.create',
                'label' => 'Créer des clients',
                'module' => 'clients',
            ],
            [
                'name' => 'clients.edit',
                'label' => 'Modifier les clients',
                'module' => 'clients',
            ],
            [
                'name' => 'clients.delete',
                'label' => 'Supprimer les clients',
                'module' => 'clients',
            ],

            /*
            |--------------------------------------------------------------------------
            | FOURNISSEURS
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'suppliers.view',
                'label' => 'Voir les fournisseurs',
                'module' => 'suppliers',
            ],
            [
                'name' => 'suppliers.create',
                'label' => 'Créer des fournisseurs',
                'module' => 'suppliers',
            ],
            [
                'name' => 'suppliers.edit',
                'label' => 'Modifier les fournisseurs',
                'module' => 'suppliers',
            ],
            [
                'name' => 'suppliers.delete',
                'label' => 'Supprimer les fournisseurs',
                'module' => 'suppliers',
            ],

            /*
            |--------------------------------------------------------------------------
            | ACHATS
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'purchases.view',
                'label' => 'Consulter les achats',
                'module' => 'purchases',
            ],
            [
                'name' => 'purchases.create',
                'label' => 'Créer un achat',
                'module' => 'purchases',
            ],
            [
                'name' => 'purchases.edit',
                'label' => 'Modifier les achats',
                'module' => 'purchases',
            ],
            [
                'name' => 'purchases.cancel',
                'label' => 'Annuler un achat',
                'module' => 'purchases',
            ],

            /*
            |--------------------------------------------------------------------------
            | VENTES
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'sales.view',
                'label' => 'Voir les ventes',
                'module' => 'sales',
            ],
            [
                'name' => 'sales.create',
                'label' => 'Créer des ventes',
                'module' => 'sales',
            ],
            [
                'name' => 'sales.cancel',
                'label' => 'Annuler les ventes',
                'module' => 'sales',
            ],

            /*
            |--------------------------------------------------------------------------
            | PAIEMENTS
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'payments.view',
                'label' => 'Voir les paiements',
                'module' => 'payments',
            ],
            [
                'name' => 'payments.create',
                'label' => 'Enregistrer des paiements',
                'module' => 'payments',
            ],
            [
                'name' => 'payments.cancel',
                'label' => 'Annuler les paiements',
                'module' => 'payments',
            ],

            /*
            |--------------------------------------------------------------------------
            | FINANCES
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'finance.view',
                'label' => 'Voir les finances',
                'module' => 'finance',
            ],

            /*
            |--------------------------------------------------------------------------
            | RAPPORTS
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'reports.view',
                'label' => 'Voir les rapports',
                'module' => 'reports',
            ],
            [
                'name' => 'reports.export',
                'label' => 'Exporter les rapports',
                'module' => 'reports',
            ],

            /*
            |--------------------------------------------------------------------------
            | PRESTATIONS
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'services.view',
                'label' => 'Voir les prestations',
                'module' => 'services',
            ],
            [
                'name' => 'services.create',
                'label' => 'Créer des prestations',
                'module' => 'services',
            ],
            [
                'name' => 'services.edit',
                'label' => 'Modifier les prestations',
                'module' => 'services',
            ],
            [
                'name' => 'services.delete',
                'label' => 'Supprimer les prestations',
                'module' => 'services',
            ],

            /*
            |--------------------------------------------------------------------------
            | UTILISATEURS
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'users.view',
                'label' => 'Voir les utilisateurs',
                'module' => 'users',
            ],
            [
                'name' => 'users.create',
                'label' => 'Créer des utilisateurs',
                'module' => 'users',
            ],
            [
                'name' => 'users.edit',
                'label' => 'Modifier les utilisateurs',
                'module' => 'users',
            ],
            [
                'name' => 'users.delete',
                'label' => 'Supprimer les utilisateurs',
                'module' => 'users',
            ],

            /*
            |--------------------------------------------------------------------------
            | PARAMÈTRES
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'settings.view',
                'label' => 'Voir les paramètres',
                'module' => 'settings',
            ],
            [
                'name' => 'settings.edit',
                'label' => 'Modifier les paramètres',
                'module' => 'settings',
            ],

            /*
            |--------------------------------------------------------------------------
            | E-COMMERCE
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'ecommerce.view',
                'label' => 'Voir le module e-commerce',
                'module' => 'ecommerce',
                'description' => 'Voir le module e-commerce',
            ],
            [
                'name' => 'ecommerce.create',
                'label' => 'Ajouter des produits au catalogue e-commerce',
                'module' => 'ecommerce',
                'description' => 'Ajouter des produits au catalogue e-commerce',
            ],
            [
                'name' => 'ecommerce.edit',
                'label' => 'Modifier le catalogue e-commerce',
                'module' => 'ecommerce',
                'description' => 'Modifier le catalogue e-commerce',
            ],
            [
                'name' => 'ecommerce.delete',
                'label' => 'Retirer des produits du catalogue e-commerce',
                'module' => 'ecommerce',
                'description' => 'Retirer des produits du catalogue e-commerce',
            ],

            /*
            |--------------------------------------------------------------------------
            | SITE WEB
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'website.view',
                'label' => 'Accéder au module Site Web',
                'module' => 'website',
            ],
            [
                'name' => 'website.create',
                'label' => 'Créer des pages et du contenu web',
                'module' => 'website',
            ],
            [
                'name' => 'website.edit',
                'label' => 'Modifier le site web',
                'module' => 'website',
            ],
            [
                'name' => 'website.delete',
                'label' => 'Supprimer des contenus web',
                'module' => 'website',
            ],
            [
                'name' => 'website.publish',
                'label' => 'Publier le site web',
                'module' => 'website',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
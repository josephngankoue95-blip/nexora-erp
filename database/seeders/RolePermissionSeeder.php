<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
   public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS PAR RÔLE
        |--------------------------------------------------------------------------
        */
        $roles = [
            /*
            |--------------------------------------------------------------------------
            | MANAGER
            |--------------------------------------------------------------------------
            */
            'manager' => [
                // Produits
                'products.view',
                'products.create',
                'products.edit',

                // Catégories
                'categories.view',
                'categories.create',
                'categories.edit',

                // Stock
                'stock.view',
                'stock.entry',
                'stock.exit',
                'stock.adjust',

                // Clients
                'clients.view',
                'clients.create',
                'clients.edit',

                // Fournisseurs
                'suppliers.view',
                'suppliers.create',
                'suppliers.edit',

                // Achats
                'purchases.view',
                'purchases.create',
                'purchases.edit',
                'purchases.cancel',

                // Ventes
                'sales.view',
                'sales.create',
                'sales.cancel',

                // Paiements
                'payments.view',
                'payments.create',
                'payments.cancel',

                // Finances
                'finance.view',

                // Rapports
                'reports.view',
                'reports.export',

                // Prestations
                'services.view',
                'services.create',
                'services.edit',
                'services.delete',
            ],

            /*
            |--------------------------------------------------------------------------
            | VENDEUR
            |--------------------------------------------------------------------------
            */
            'seller' => [
                'products.view',

                'clients.view',
                'clients.create',
                'clients.edit',

                'sales.view',
                'sales.create',
            ],

            /*
            |--------------------------------------------------------------------------
            | CAISSIER
            |--------------------------------------------------------------------------
            */
            'cashier' => [
                'products.view',

                'clients.view',
                'clients.create',

                'sales.view',

                'payments.view',
                'payments.create',
            ],

            /*
            |--------------------------------------------------------------------------
            | GESTIONNAIRE DE STOCK
            |--------------------------------------------------------------------------
            */
            'stock_manager' => [
                // Produits
                'products.view',
                'products.create',
                'products.edit',

                // Catégories
                'categories.view',
                'categories.create',
                'categories.edit',

                // Stock
                'stock.view',
                'stock.entry',
                'stock.exit',
                'stock.adjust',

                // Clients
                'clients.view',
                'clients.create',
                'clients.edit',

                // Fournisseurs
                'suppliers.view',
                'suppliers.create',
                'suppliers.edit',

                // Achats
                'purchases.view',
                'purchases.create',
                'purchases.edit',

                // Ventes
                'sales.view',
                'sales.create',

                // Paiements
                'payments.view',
                'payments.create',

                // Finances
                'finance.view',

                // Rapports
                'reports.view',
                'reports.export',

                // Prestations
                'services.view',
                'services.create',
                'services.edit',
                'services.delete',
            ],

            /*
            |--------------------------------------------------------------------------
            | COMPTABLE
            |--------------------------------------------------------------------------
            */
            'accountant' => [
                'sales.view',

                'payments.view',
                'payments.create',
                'payments.cancel',

                'purchases.view',

                'finance.view',

                'reports.view',
                'reports.export',
            ],

            /*
            |--------------------------------------------------------------------------
            | EMPLOYÉ
            |--------------------------------------------------------------------------
            */
            'employee' => [
                'products.view',
                'clients.view',
                'sales.view',
            ],

            /*
            |--------------------------------------------------------------------------
            | DIRECTEUR
            |--------------------------------------------------------------------------
            | Supervision et consultation.
            |--------------------------------------------------------------------------
            */
            'director' => [
                'products.view',
                'categories.view',
                'stock.view',

                'clients.view',
                'suppliers.view',

                'purchases.view',
                'sales.view',

                'payments.view',
                'finance.view',

                'reports.view',
                'services.view',

                'users.view',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | RECRÉER LES ASSOCIATIONS RÔLE / PERMISSION
        |--------------------------------------------------------------------------
        |
        | Les permissions qui n'existent pas encore sont ignorées.
        | PermissionSeeder doit donc être exécuté avant ce seeder.
        |
        */
        RolePermission::query()->delete();

        foreach ($roles as $role => $permissionNames) {
            $permissionNames = array_unique($permissionNames);

            foreach ($permissionNames as $permissionName) {
                $permission = Permission::query()
                    ->where('name', $permissionName)
                    ->first();

                if (!$permission) {
                    continue;
                }

                RolePermission::create([
                    'role' => $role,
                    'permission_id' => $permission->id,
                ]);
            }
        }
    }
}
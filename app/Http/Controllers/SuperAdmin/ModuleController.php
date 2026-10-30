<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    /**
     * Modules regroupés dans Gestion de stock.
     */
    private function stockModuleSlugs(): array
    {
        return [
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
        ];
    }

    /**
     * Afficher les modules d'une entreprise.
     */
    public function edit(Company $company)
    {
        $stockSlugs = $this->stockModuleSlugs();

        // Charger uniquement les modules actifs du catalogue.
        $allModules = Module::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Récupérer le module parent Gestion de stock.
        $stockModule = $allModules->firstWhere(
            'slug',
            'inventory'
        );

        /*
         * Masquer les modules internes de Gestion de stock.
         * Les autres modules restent visibles.
         */
        $modules = $allModules
            ->reject(function ($module) use ($stockSlugs) {
                return in_array(
                    $module->slug,
                    $stockSlugs,
                    true
                );
            })
            ->values();

        /*
         * Ajouter Gestion de stock à la liste visible.
         * Ce module doit exister dans la table modules.
         */
       // Ajouter Gestion de stock une seule fois.
if ($stockModule) {
    $modules = $modules
        ->reject(fn ($module) => $module->id === $stockModule->id)
        ->push($stockModule)
        ->unique('id')
        ->sortBy('sort_order')
        ->values();
}

        // Modules réellement activés pour l'entreprise.
        $enabledModuleIds = DB::table('company_modules')
            ->where('company_id', $company->id)
            ->where('is_active', true)
            ->pluck('module_id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        /*
         * Si au moins un module interne est actif,
         * cocher Gestion de stock dans l'interface.
         */
        if ($stockModule) {
            $activeStockChild = Module::query()
                ->where('is_active', true)
                ->whereIn('slug', $stockSlugs)
                ->whereIn('id', $enabledModuleIds)
                ->exists();

            if ($activeStockChild) {
                $enabledModuleIds[] = (int) $stockModule->id;
            }
        }

        return view(
            'superadmin.companies.modules',
            compact(
                'company',
                'modules',
                'enabledModuleIds'
            )
        );
    }

    /**
     * Enregistrer les modules de l'entreprise.
     */
    public function update(
        Request $request,
        Company $company
    ) {
        $validated = $request->validate([
            'modules' => [
                'nullable',
                'array',
            ],

            'modules.*' => [
                'integer',
                'distinct',
                'exists:modules,id',
            ],
        ]);

        $selectedModules = array_map(
            'intval',
            $validated['modules'] ?? []
        );

        $stockSlugs = $this->stockModuleSlugs();

        $stockModule = Module::query()
            ->where('slug', 'inventory')
            ->where('is_active', true)
            ->first();

        /*
         * Déterminer si Gestion de stock est sélectionné.
         */
        $stockSelected = $stockModule
            && in_array(
                (int) $stockModule->id,
                $selectedModules,
                true
            );

        /*
         * Retirer l'identifiant parent de la liste temporaire.
         * Les modules internes seront activés à sa place.
         */
        if ($stockModule) {
            $selectedModules = array_values(
                array_filter(
                    $selectedModules,
                    fn ($id) => $id !== (int) $stockModule->id
                )
            );
        }

        DB::transaction(function () use (
            $company,
            &$selectedModules,
            $stockSelected,
            $stockModule,
            $stockSlugs
        ) {
            /*
             * Désactiver les anciennes sélections.
             */
            DB::table('company_modules')
                ->where('company_id', $company->id)
                ->update([
                    'is_active' => false,
                    'deactivated_at' => now(),
                    'updated_at' => now(),
                ]);

            /*
             * Si Gestion de stock est sélectionné,
             * ajouter tous ses modules internes actifs.
             */
            if ($stockSelected && $stockModule) {
                $childIds = Module::query()
                    ->where('is_active', true)
                    ->whereIn('slug', $stockSlugs)
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->toArray();

                $selectedModules = array_values(
                    array_unique(
                        array_merge(
                            $selectedModules,
                            $childIds
                        )
                    )
                );

                // Enregistrer également le module parent.
                $selectedModules[] = (int) $stockModule->id;
            }

            /*
             * Activer les modules sélectionnés.
             */
            foreach (array_unique($selectedModules) as $moduleId) {
                DB::table('company_modules')->updateOrInsert(
                    [
                        'company_id' => $company->id,
                        'module_id' => $moduleId,
                    ],
                    [
                        'is_active' => true,
                        'activated_at' => now(),
                        'deactivated_at' => null,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        });

        return redirect()
            ->route(
                'superadmin.companies.modules.edit',
                $company
            )
            ->with(
                'success',
                'Les modules de l’entreprise ont été mis à jour avec succès.'
            );
    }
}
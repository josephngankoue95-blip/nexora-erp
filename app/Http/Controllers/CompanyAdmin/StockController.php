<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Tableau général du stock
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Product::with('category')
            ->where('company_id', $companyId);

        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRE STOCK
        |--------------------------------------------------------------------------
        */

        if ($request->status === 'available') {

            $query->where('stock_quantity', '>', 0);

        } elseif ($request->status === 'low') {

            $query->whereColumn(
                'stock_quantity',
                '<=',
                'min_stock'
            );

            $query->where(
                'stock_quantity',
                '>',
                0
            );

        } elseif ($request->status === 'out') {

            $query->where(
                'stock_quantity',
                '<=',
                0
            );
        }

        $products = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES
        |--------------------------------------------------------------------------
        */

        $totalProducts = Product::where(
            'company_id',
            $companyId
        )->count();

        $totalQuantity = Product::where(
            'company_id',
            $companyId
        )->sum('stock_quantity');

        $lowStock = Product::where(
            'company_id',
            $companyId
        )
            ->whereColumn(
                'stock_quantity',
                '<=',
                'min_stock'
            )
            ->where(
                'stock_quantity',
                '>',
                0
            )
            ->count();

        $outOfStock = Product::where(
            'company_id',
            $companyId
        )
            ->where(
                'stock_quantity',
                '<=',
                0
            )
            ->count();

        return view(
            'company.stock.index',
            compact(
                'products',
                'totalProducts',
                'totalQuantity',
                'lowStock',
                'outOfStock'
            )
        );
    }


    /**
     * Formulaire d'entrée
     */
    public function createEntry()
    {
        $companyId = auth()->user()->company_id;

        $products = Product::where(
            'company_id',
            $companyId
        )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'company.stock.entry',
            compact('products')
        );
    }


    /**
     * Enregistrer une entrée
     */
    public function storeEntry(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([

            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'reason' => [
                'required',
                'string',
                'max:255',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],

        ]);

        DB::transaction(function () use (
            $validated,
            $companyId
        ) {

            /*
            |--------------------------------------------------------------------------
            | PRODUIT DE L'ENTREPRISE UNIQUEMENT
            |--------------------------------------------------------------------------
            */

            $product = Product::where(
                'company_id',
                $companyId
            )
                ->lockForUpdate()
                ->findOrFail(
                    $validated['product_id']
                );


            $stockBefore = $product->stock_quantity;

            $stockAfter =
                $stockBefore
                + $validated['quantity'];


            /*
            |--------------------------------------------------------------------------
            | MISE À JOUR STOCK
            |--------------------------------------------------------------------------
            */

            $product->update([
                'stock_quantity' => $stockAfter,
            ]);


            /*
            |--------------------------------------------------------------------------
            | HISTORIQUE
            |--------------------------------------------------------------------------
            */

            StockMovement::create([

                'company_id' => $companyId,

                'product_id' => $product->id,

                'user_id' => auth()->id(),

                'type' => 'entry',

                'quantity' => $validated['quantity'],

                'stock_before' => $stockBefore,

                'stock_after' => $stockAfter,

                'reason' => $validated['reason'],

                'note' => $validated['note'] ?? null,
            ]);
        });

        return redirect()
            ->route('company.stock.index')
            ->with(
                'success',
                'Entrée de stock enregistrée avec succès.'
            );
    }


    /**
     * Formulaire de sortie
     */
    public function createExit()
    {
        $companyId = auth()->user()->company_id;

        $products = Product::where(
            'company_id',
            $companyId
        )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'company.stock.exit',
            compact('products')
        );
    }


    /**
     * Enregistrer une sortie
     */
    public function storeExit(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([

            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'reason' => [
                'required',
                'string',
                'max:255',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],

        ]);

        DB::transaction(function () use (
            $validated,
            $companyId
        ) {

            $product = Product::where(
                'company_id',
                $companyId
            )
                ->lockForUpdate()
                ->findOrFail(
                    $validated['product_id']
                );


            $stockBefore = $product->stock_quantity;

            /*
            |--------------------------------------------------------------------------
            | EMPÊCHER STOCK NÉGATIF
            |--------------------------------------------------------------------------
            */

            if (
                $validated['quantity']
                > $stockBefore
            ) {

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' =>
                        'Stock insuffisant. Stock disponible : '
                        . $stockBefore,
                ]);
            }


            $stockAfter =
                $stockBefore
                - $validated['quantity'];


            /*
            |--------------------------------------------------------------------------
            | MISE À JOUR
            |--------------------------------------------------------------------------
            */

            $product->update([
                'stock_quantity' => $stockAfter,
            ]);


            /*
            |--------------------------------------------------------------------------
            | HISTORIQUE
            |--------------------------------------------------------------------------
            */

            StockMovement::create([

                'company_id' => $companyId,

                'product_id' => $product->id,

                'user_id' => auth()->id(),

                'type' => 'exit',

                'quantity' => $validated['quantity'],

                'stock_before' => $stockBefore,

                'stock_after' => $stockAfter,

                'reason' => $validated['reason'],

                'note' => $validated['note'] ?? null,
            ]);
        });

        return redirect()
            ->route('company.stock.index')
            ->with(
                'success',
                'Sortie de stock enregistrée avec succès.'
            );
    }


    /**
     * Historique
     */
    public function history(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = StockMovement::with([
            'product',
            'user',
        ])
            ->where(
                'company_id',
                $companyId
            );

        if ($request->filled('type')) {

            $query->where(
                'type',
                $request->type
            );
        }

        if ($request->filled('search')) {

            $search = $request->search;

            $query->whereHas(
                'product',
                function ($q) use ($search) {

                    $q->where(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'sku',
                        'like',
                        "%{$search}%"
                    );
                }
            );
        }

        $movements = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view(
            'company.stock.history',
            compact('movements')
        );
    }
}
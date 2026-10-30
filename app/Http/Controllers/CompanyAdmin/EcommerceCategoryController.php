<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\EcommerceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EcommerceCategoryController extends Controller
{
    private function companyId(): int
    {
        $companyId = auth()->user()?->company_id;

        abort_unless($companyId, 403);

        return (int) $companyId;
    }

    private function category(int $id): EcommerceCategory
    {
        return EcommerceCategory::query()
            ->where('company_id', $this->companyId())
            ->findOrFail($id);
    }

    private function generateSlug(
        string $name,
        int $companyId,
        ?int $ignoreId = null
    ): string {
        $base = Str::slug($name) ?: 'categorie';
        $slug = $base;
        $counter = 2;

        while (
            EcommerceCategory::query()
                ->where('company_id', $companyId)
                ->where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) => $query->whereKeyNot($ignoreId)
                )
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function validatedData(Request $request): array
    {
        $companyId = $this->companyId();

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'description' => [
                'nullable',
                'string',
                'max:5000'
            ],

            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('ecommerce_categories', 'id')
                    ->where('company_id', $companyId),
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0'
            ],

            'is_active' => [
                'nullable',
                'boolean'
            ],
        ]);
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId();

        $search = trim((string) $request->query('search', ''));

        $status = $request->query('status', 'all');

        $query = EcommerceCategory::query()
            ->where('company_id', $companyId)
            ->with('parent')
            ->withCount('children');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $categories = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $totalCategories = EcommerceCategory::query()
            ->where('company_id', $companyId)
            ->count();

        $activeCategories = EcommerceCategory::query()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->count();

        $inactiveCategories = EcommerceCategory::query()
            ->where('company_id', $companyId)
            ->where('is_active', false)
            ->count();

        return view(
            'company.ecommerce.categories.index',
            compact(
                'categories',
                'totalCategories',
                'activeCategories',
                'inactiveCategories',
                'search',
                'status'
            )
        );
    }

    public function create()
    {
        $parents = EcommerceCategory::query()
            ->where('company_id', $this->companyId())
            ->orderBy('name')
            ->get();

        return view(
            'company.ecommerce.categories.create',
            compact('parents')
        );
    }

    public function store(Request $request)
    {
        $companyId = $this->companyId();

        $data = $this->validatedData($request);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request
                ->file('image')
                ->store(
                    'ecommerce/categories/' . $companyId,
                    'public'
                );
        }

        try {
            EcommerceCategory::create([
                'company_id' => $companyId,
                'parent_id' => $data['parent_id'] ?? null,
                'name' => $data['name'],
                'slug' => $this->generateSlug(
                    $data['name'],
                    $companyId
                ),
                'description' => $data['description'] ?? null,
                'image' => $imagePath,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $request->boolean('is_active'),
            ]);
        } catch (\Throwable $e) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $e;
        }

        return redirect()
            ->route('company.ecommerce.categories.index')
            ->with(
                'success',
                'Catégorie créée avec succès.'
            );
    }

    public function edit(int $id)
    {
        $category = $this->category($id);

        $parents = EcommerceCategory::query()
            ->where('company_id', $this->companyId())
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view(
            'company.ecommerce.categories.edit',
            compact('category', 'parents')
        );
    }

    public function update(Request $request, int $id)
    {
        $companyId = $this->companyId();
        $category = $this->category($id);

        $data = $this->validatedData($request);

        $parentId = $data['parent_id'] ?? null;

        if ($parentId !== null) {
            $parentId = (int) $parentId;

            if ($parentId === $category->id) {
                throw ValidationException::withMessages([
                    'parent_id' =>
                        'Une catégorie ne peut pas être sa propre parente.',
                ]);
            }

            // Empêcher les relations circulaires.
            $ancestorId = $parentId;
            $visited = [];

            while ($ancestorId !== null) {
                if (
                    $ancestorId === $category->id ||
                    isset($visited[$ancestorId])
                ) {
                    throw ValidationException::withMessages([
                        'parent_id' =>
                            'Cette catégorie parente créerait une relation circulaire.',
                    ]);
                }

                $visited[$ancestorId] = true;

                $ancestor = EcommerceCategory::query()
                    ->where('company_id', $companyId)
                    ->find($ancestorId);

                $ancestorId = $ancestor?->parent_id
                    ? (int) $ancestor->parent_id
                    : null;
            }
        }

        $oldImage = $category->image;
        $newImage = null;

        if ($request->hasFile('image')) {
            $newImage = $request
                ->file('image')
                ->store(
                    'ecommerce/categories/' . $companyId,
                    'public'
                );
        }

        try {
            $category->update([
                'parent_id' => $parentId,
                'name' => $data['name'],
                'slug' => $this->generateSlug(
                    $data['name'],
                    $companyId,
                    $category->id
                ),
                'description' => $data['description'] ?? null,
                'image' => $newImage ?? $oldImage,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $request->boolean('is_active'),
            ]);
        } catch (\Throwable $e) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            throw $e;
        }

        if ($newImage && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('company.ecommerce.categories.index')
            ->with(
                'success',
                'Catégorie modifiée avec succès.'
            );
    }

    public function toggleStatus(int $id)
    {
        $category = $this->category($id);

        $category->update([
            'is_active' => !$category->is_active,
        ]);

        return back()->with(
            'success',
            'Statut de la catégorie mis à jour.'
        );
    }

    public function destroy(int $id)
    {
        $category = $this->category($id);

        if (
            EcommerceCategory::query()
                ->where('company_id', $this->companyId())
                ->where('parent_id', $category->id)
                ->exists()
        ) {
            return back()->with(
                'error',
                'Impossible de supprimer cette catégorie : elle possède des sous-catégories.'
            );
        }

        // La vérification des produits associés sera ajoutée
        // lors de l'intégration de la relation produits-catégories.

        $image = $category->image;

        DB::transaction(function () use ($category) {

        
            if ($category->ecommerceProducts()->exists()) {
                return back()->with(
                    'error',
                    'Impossible de supprimer cette catégorie : des produits E-commerce y sont associés.'
                );
            }

            $category->delete();
        });

        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return redirect()
            ->route('company.ecommerce.categories.index')
            ->with(
                'success',
                'Catégorie supprimée avec succès.'
            );
    }
}

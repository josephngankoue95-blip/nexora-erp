<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\EcommerceReview;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EcommerceReviewController extends Controller
{
    /**
     * Récupérer l'identifiant de l'entreprise connectée.
     */
    private function companyId(): int|string|null
    {
        return auth()->user()?->company_id
            ?? session('company_id')
            ?? session('current_company_id');
    }

    /**
     * Afficher la liste des avis clients.
     */
    public function index(Request $request): View
    {
        $companyId = $this->companyId();

        $baseQuery = EcommerceReview::forCompany($companyId);

        $reviews = (clone $baseQuery)
            ->when(
                in_array($request->input('status'), ['pending', 'published', 'rejected'], true),
                fn ($query) => $query->where('status', $request->input('status'))
            )
            ->when(
                $request->filled('rating')
                    && in_array((int) $request->input('rating'), [1, 2, 3, 4, 5], true),
                fn ($query) => $query->where('rating', (int) $request->input('rating'))
            )
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = trim((string) $request->input('q'));

                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('comment', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $counts = [
            'all' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'published' => (clone $baseQuery)->where('status', 'published')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];

        $averageRating = round(
            (float) (clone $baseQuery)
                ->where('status', 'published')
                ->avg('rating'),
            1
        );

        return view(
            'company.ecommerce.reviews.index',
            compact('reviews', 'counts', 'averageRating')
        );
    }

    /**
     * Afficher le détail d'un avis.
     */
    public function show(EcommerceReview $review): View
    {
        $this->authorizeCompanyReview($review);

        return view('company.ecommerce.reviews.show', compact('review'));
    }

    /**
     * Modifier le statut d'un avis.
     */
    public function updateStatus(
        Request $request,
        EcommerceReview $review
    ): RedirectResponse {
        $this->authorizeCompanyReview($review);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,published,rejected'],
        ]);

        $review->status = $validated['status'];

        $review->published_at = $validated['status'] === 'published'
            ? ($review->published_at ?? now())
            : null;

        $review->save();

        return redirect()
            ->route('company.ecommerce.reviews.index')
            ->with('success', 'Le statut de l’avis a été mis à jour.');
    }

    /**
     * Supprimer un avis.
     */
    public function destroy(EcommerceReview $review): RedirectResponse
    {
        $this->authorizeCompanyReview($review);

        $review->delete();

        return redirect()
            ->route('company.ecommerce.reviews.index')
            ->with('success', 'L’avis client a été supprimé.');
    }

    /**
     * Empêcher l'accès aux avis d'une autre entreprise.
     */
    private function authorizeCompanyReview(EcommerceReview $review): void
    {
        $companyId = $this->companyId();

        abort_unless(
            $companyId !== null
            && (string) $review->company_id === (string) $companyId,
            404
        );
    }
}
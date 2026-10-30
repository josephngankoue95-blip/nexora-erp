<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\WebsitePage;
use Illuminate\Http\Request;
use App\Support\WebsiteHtml;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WebsitePageController extends Controller
{
    private function companyId(): int
    {
        $id = auth()->user()?->company_id;
        abort_unless($id, 403);
        return (int) $id;
    }

    private function owned(int $id): WebsitePage
    {
        return WebsitePage::query()->where('company_id', $this->companyId())->findOrFail($id);
    }

    public function index(Request $request): View
    {
        $pages = WebsitePage::query()->where('company_id', $this->companyId())
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%' . trim((string) $request->q) . '%'))
            ->latest()->paginate(12)->withQueryString();
        return view('company.website.pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('company.website.pages.form', ['page' => new WebsitePage()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['company_id'] = $this->companyId();
        $data['published_at'] = $data['status'] === 'published' ? now() : null;
        WebsitePage::create($data);
        return redirect()->route('company.website.pages.index')->with('success', 'Page créée avec succès.');
    }

    public function edit(int $page): View
    {
        return view('company.website.pages.form', ['page' => $this->owned($page)]);
    }

    public function update(Request $request, int $page): RedirectResponse
    {
        $model = $this->owned($page);
        $data = $this->validated($request, $model->id);
        $data['published_at'] = $data['status'] === 'published'
            ? ($model->published_at ?? now()) : null;
        $model->update($data);
        return redirect()->route('company.website.pages.index')->with('success', 'Page mise à jour.');
    }

    public function destroy(int $page): RedirectResponse
    {
        $this->owned($page)->delete();
        return redirect()->route('company.website.pages.index')->with('success', 'Page supprimée.');
    }

    private function validated(Request $request, ?int $pageId = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('title', ''))]);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:180', Rule::unique('website_pages', 'slug')
                ->where('company_id', $this->companyId())->ignore($pageId)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string', 'max:100000'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'show_in_menu' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
        ]);
        $data['show_in_menu'] = $request->boolean('show_in_menu');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        
        $data['content'] = WebsiteHtml::clean(
            $data['content'] ?? ''
        );

        return $data;
    }

    
/**
 * Prévisualiser une page, même en brouillon.
 */
public function preview(int $page)
{
    $companyId = auth()->user()?->company_id;

    abort_unless($companyId, 403);

    $websitePage = \App\Models\WebsitePage::query()
        ->where('company_id', $companyId)
        ->whereKey($page)
        ->firstOrFail();

    $company = \App\Models\Company::query()
        ->whereKey($companyId)
        ->firstOrFail();

    return view('company.website.pages.preview', [
        'page' => $websitePage,
        'company' => $company,
    ]);
}

}


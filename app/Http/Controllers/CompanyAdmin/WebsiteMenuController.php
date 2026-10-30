<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteMenuItem;
use App\Models\WebsitePage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WebsiteMenuController extends Controller
{
    private function companyId(): int
    {
        $id = auth()->user()?->company_id;
        abort_unless($id, 403);
        return (int) $id;
    }

    public function index()
    {
        $companyId = $this->companyId();
        $items = WebsiteMenuItem::where('company_id', $companyId)->with('page')->orderBy('sort_order')->orderBy('id')->get();
        $pages = WebsitePage::where('company_id', $companyId)->orderBy('title')->get(['id','title','status']);
        return view('company.website.menus.index', compact('items', 'pages'));
    }

    private function validated(Request $request, int $companyId): array
    {
        $data = $request->validate([
            'label' => ['required','string','max:100'],
            'type' => ['required', Rule::in(['page','external'])],
            'website_page_id' => ['nullable', Rule::exists('website_pages','id')->where('company_id', $companyId)],
            'url' => ['nullable','string','max:2048'],
            'is_visible' => ['sometimes','boolean'],
        ]);
        if ($data['type'] === 'page') {
            if (empty($data['website_page_id'])) {
                throw \Illuminate\Validation\ValidationException::withMessages(['website_page_id' => 'Choisissez une page.']);
            }
            $data['url'] = null;
        } else {
            $url = trim($data['url'] ?? '');
            if (!filter_var($url, FILTER_VALIDATE_URL) || !in_array(strtolower(parse_url($url, PHP_URL_SCHEME) ?? ''), ['http','https'], true)) {
                throw \Illuminate\Validation\ValidationException::withMessages(['url' => 'Entrez une URL complète en https:// ou http://.']);
            }
            $data['url'] = $url;
            $data['website_page_id'] = null;
        }
        $data['is_visible'] = $request->boolean('is_visible');
        return $data;
    }

    public function store(Request $request)
    {
        $companyId = $this->companyId();
        $data = $this->validated($request, $companyId);
        $data['company_id'] = $companyId;
        $data['sort_order'] = (int) WebsiteMenuItem::where('company_id', $companyId)->max('sort_order') + 1;
        WebsiteMenuItem::create($data);
        return back()->with('success', 'Élément ajouté au menu.');
    }

    public function update(Request $request, int $menu)
    {
        $companyId = $this->companyId();
        $item = WebsiteMenuItem::where('company_id', $companyId)->findOrFail($menu);
        $item->update($this->validated($request, $companyId));
        return back()->with('success', 'Élément modifié.');
    }

    public function move(int $menu, string $direction)
    {
        abort_unless(in_array($direction, ['up','down'], true), 404);
        $companyId = $this->companyId();
        $item = WebsiteMenuItem::where('company_id', $companyId)->findOrFail($menu);
        $query = WebsiteMenuItem::where('company_id', $companyId);
        $neighbor = $direction === 'up'
            ? (clone $query)->where('sort_order', '<', $item->sort_order)->orderByDesc('sort_order')->first()
            : (clone $query)->where('sort_order', '>', $item->sort_order)->orderBy('sort_order')->first();
        if ($neighbor) {
            $order = $item->sort_order;
            $item->update(['sort_order' => $neighbor->sort_order]);
            $neighbor->update(['sort_order' => $order]);
        }
        return back()->with('success', 'Ordre mis à jour.');
    }

    public function destroy(int $menu)
    {
        WebsiteMenuItem::where('company_id', $this->companyId())->findOrFail($menu)->delete();
        return back()->with('success', 'Élément supprimé.');
    }
}

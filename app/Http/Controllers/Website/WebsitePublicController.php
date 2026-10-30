<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use App\Models\Company;
use App\Models\WebsitePage;
use App\Models\WebsiteMenuItem;
use Illuminate\View\View;

class WebsitePublicController extends Controller
{
    /**
     * Pages publiées de l'entreprise.
     */
    private function publishedPages(Company $company)
    {
        abort_unless($company->is_active, 404);

        return WebsitePage::query()
            ->where('company_id', $company->id)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Récupérer les menus de l'entreprise.
     *
     * Les noms de colonnes ci-dessous sont
     * à vérifier avec WebsiteMenuItem.
     */
    private function navigation(Company $company, $pages)
    {

        $items = WebsiteMenuItem::query()
            ->where('company_id', $company->id)
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();


        $navigation = collect();

        foreach ($items as $item) {

            // if (!$item->is_visible) {
            //     continue;
            // }

            if ($item->type === 'page') {

                $page = $pages->firstWhere(
                    'id',
                    $item->website_page_id
                );

                if (!$page) {
                    continue;
                }

                $url = $page->slug === 'accueil'
                    ? route('website.public.index', [
                        'company' => $company->slug
                    ])
                    : route('website.public.show', [
                        'company' => $company->slug,
                        'slug' => $page->slug
                    ]);

            } elseif ($item->type === 'external') {

                $url = $item->url;

                if (
                    !is_string($url) ||
                    !preg_match('~^https?://~i', $url)
                ) {
                    continue;
                }

            } else {
                continue;
            }

            $navigation->push([
                'label' => $item->label,
                'url' => $url,
            ]);
        }

        /**
         * Aucun menu personnalisé :
         * conserver la navigation actuelle.
         */
        if ($items->isEmpty()) {

            $navigation->push([
                'label' => 'Accueil',
                'url' => route('website.public.index', [
                    'company' => $company->slug
                ]),
            ]);

            foreach (
                $pages->where('show_in_menu', true)
                    ->where('slug', '!=', 'accueil')
                as $page
            ) {
                $navigation->push([
                    'label' => $page->title,
                    'url' => route('website.public.show', [
                        'company' => $company->slug,
                        'slug' => $page->slug
                    ]),
                ]);
            }
        }

        return $navigation;
    }

    /**
     * Accueil du site public.
     */
    public function index(Company $company): View
    {
        $pages = $this->publishedPages($company)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $home = $pages->firstWhere(
            'slug',
            'accueil'
        );

        $navigation = $this->navigation(
            $company,
            $pages
        );
        $settings = $this->websiteSettings($company);


        return view('website.public.index', compact(
            'company',
            'pages',
            'home',
            'navigation',
            'settings'
        ));

    }

    /**
     * Afficher une page publique.
     */
    public function show(
        Company $company,
        string $slug
    ): View {

        $page = $this->publishedPages($company)
            ->where('slug', $slug)
            ->firstOrFail();

        $pages = $this->publishedPages($company)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $navigation = $this->navigation(
            $company,
            $pages
        );
        $settings = $this->websiteSettings($company);

        return view(
            'website.public.show',
            compact(
                'company',
                'pages',
                'page',
                'navigation',
                'settings'
            )
        );
    }

    
/**
 * Paramètres visuels de l'entreprise.
 */
private function websiteSettings(Company $company): ?WebsiteSetting
{
    return WebsiteSetting::query()
        ->where('company_id', $company->id)
        ->first();
}

}
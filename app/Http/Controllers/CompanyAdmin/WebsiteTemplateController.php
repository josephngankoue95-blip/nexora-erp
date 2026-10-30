<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\WebsitePage;
use App\Models\WebsiteSetting;
use App\Support\WebsiteTemplates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Str;

class WebsiteTemplateController extends Controller
{
    public function index(): View
    {
        $templates = WebsiteTemplates::all();

        return view(
            'company.website.templates.index',
            compact('templates')
        );
    }

    
public function install(Request $request, string $template)
{
    $selected = WebsiteTemplates::find($template);

    abort_unless($selected !== null, 404);

    $companyId = $request->user()->company_id;

    abort_unless($companyId, 403);

    $created = 0;

    DB::transaction(function () use (
        $companyId,
        $template,
        $selected,
        &$created
    ) {

        // Enregistrer le thème choisi pour cette entreprise.
        WebsiteSetting::query()->updateOrCreate(
            ['company_id' => $companyId],
            ['theme_key' => $template]
        );

        // Conserver les pages existantes.
        foreach ($selected['pages'] as $slug => $title) {

            $exists = WebsitePage::query()
                ->where('company_id', $companyId)
                ->where('slug', $slug)
                ->exists();

            if ($exists) {
                continue;
            }

            WebsitePage::create([
                'company_id' => $companyId,
                'title' => $title,
                'slug' => $slug,
                'excerpt' => null,
                'content' => '',
                'status' => 'draft',
                'show_in_menu' => true,
                'sort_order' => 0,
                'published_at' => null,
            ]);

            $created++;
        }

    });

    return redirect()
        ->route('company.website.pages.index')
        ->with(
            'success',
            "Modèle {$selected['name']} appliqué. "
            . "{$created} nouvelle(s) page(s) créée(s)."
        );
}

    
/**
 * Prévisualiser un modèle sans l'installer.
 */
public function preview(string $template): View
{
    $selected = WebsiteTemplates::find($template);

    abort_unless($selected !== null, 404);

    return view(
        'company.website.templates.preview',
        [
            'templateKey' => $template,
            'template' => $selected,
        ]
    );
}

}
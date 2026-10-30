<?php

namespace App\Http\Controllers\CompanyAdmin;

use App\Http\Controllers\Controller;
use App\Models\WebsitePage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WebsiteBuilderController extends Controller
{
    private const MAX_BLOCKS = 100;
    private const MAX_JSON_SIZE = 500000;

    /**
     * Blocs autorisés dans WebStudio.
     */
    private const TYPES = [
        'hero',
        'heading',
        'text',
        'image',
        'button',
        'label',
        'columns',
        'divider',
        'gallery',
        'video',
        'cards',
        'stats',
        'quote',
        'faq',
        'cta',
        'spacer',
        'carousel',
        'testimonials',
        'banner_slider',
    ];

    /**
     * Champs textuels.
     * Les booléens et nombres sont traités séparément.
     */
    private const TEXT_FIELDS = [
        'title',
        'subtitle',
        'text',
        'label',
        'url',
        'image',
        'alt',
        'background',
        'color',
        'left',
        'right',
        'third',
        'image2',
        'image3',
        'caption',
        'caption2',
        'caption3',
        'author',
        'author2',
        'author3',
        'title2',
        'title3',
        'animation',
    ];

    private const NUMERIC_FIELDS = [
        'height',
        'interval',
        'radius',
        'padding',
        'duration',
        'delay',
    ];

    private const URL_FIELDS = [
        'url',
        'image',
        'image2',
        'image3',
    ];

    private const COLOR_FIELDS = [
        'background',
        'color',
    ];

    /**
     * Récupérer uniquement les pages
     * de l'entreprise connectée.
     */
    private function page(int $id): WebsitePage
    {
        $companyId = auth()->user()?->company_id;

        abort_unless(
            $companyId,
            403,
            'Aucune entreprise associée.'
        );

        return WebsitePage::query()
            ->where('company_id', $companyId)
            ->findOrFail($id);
    }

    /**
     * Afficher le constructeur visuel.
     */
    public function edit(int $page): View
    {
        return view('company.website.builder.edit', [
            'page' => $this->page($page),
        ]);
    }

    /**
     * Enregistrer les blocs de la page.
     */
    public function update(
        Request $request,
        int $page
    ): RedirectResponse {

        $model = $this->page($page);

        $validated = $request->validate([
            'builder_content' => [
                'required',
                'json',
                'max:' . self::MAX_JSON_SIZE,
            ],
            'status' => [
                'required',
                Rule::in(['draft', 'published']),
            ],
        ]);

        $blocks = json_decode(
            $validated['builder_content'],
            true
        );

        /**
         * Vérifier la structure générale.
         */
        if (
            !is_array($blocks) ||
            !array_is_list($blocks) ||
            count($blocks) > self::MAX_BLOCKS
        ) {
            return back()
                ->withErrors([
                    'builder_content' =>
                        'Structure invalide : 100 blocs maximum.',
                ])
                ->withInput();
        }

        $clean = [];

        foreach ($blocks as $index => $block) {

            /**
             * Vérifier le type de bloc.
             */
            if (
                !is_array($block) ||
                !in_array(
                    $block['type'] ?? null,
                    self::TYPES,
                    true
                ) ||
                !is_array($block['data'] ?? null)
            ) {
                return back()
                    ->withErrors([
                        'builder_content' =>
                            'Bloc invalide à la position ' .
                            ($index + 1),
                    ])
                    ->withInput();
            }

            $data = $block['data'];
            $safe = [];

            /**
             * 1. Validation des champs texte.
             */
            foreach (self::TEXT_FIELDS as $field) {

                $value = $data[$field] ?? '';

                if (
                    !is_string($value) ||
                    mb_strlen($value) > 5000
                ) {
                    return back()
                        ->withErrors([
                            'builder_content' =>
                                'Champ invalide : ' . $field,
                        ])
                        ->withInput();
                }

                $safe[$field] = $value;
            }

            /**
             * 2. Validation des champs numériques.
             *
             * Accepte :
             * 5000
             * "5000"
             */
            foreach (self::NUMERIC_FIELDS as $field) {

                $value = $data[$field] ?? '';

                if ($value === '' || $value === null) {
                    $safe[$field] = '';
                    continue;
                }

                if (
                    !is_string($value) &&
                    !is_int($value)
                ) {
                    return back()
                        ->withErrors([
                            'builder_content' =>
                                'Valeur numérique invalide : ' .
                                $field,
                        ])
                        ->withInput();
                }

                if (
                    !ctype_digit((string) $value) ||
                    (int) $value > 30000
                ) {
                    return back()
                        ->withErrors([
                            'builder_content' =>
                                'Nombre invalide : ' . $field,
                        ])
                        ->withInput();
                }

                $number = (int) $value;
                if (($field === 'duration' && ($number < 200 || $number > 2500)) ||
                    ($field === 'delay' && $number > 2000)) {
                    return back()->withErrors(['builder_content' => 'Valeur hors limites : ' . $field])->withInput();
                }
                $safe[$field] = (string) $number;
            }

            /**
             * 3. Validation autoplay.
             *
             * Accepte les booléens JavaScript,
             * les chaînes et les anciennes valeurs.
             */
            $autoplay = $data['autoplay'] ?? 'non';

            $trueValues = [
                true,
                1,
                '1',
                'true',
                'on',
                'oui',
            ];

            $falseValues = [
                false,
                0,
                '0',
                'false',
                'off',
                'non',
                '',
            ];

            if (
                !in_array($autoplay, $trueValues, true) &&
                !in_array($autoplay, $falseValues, true)
            ) {
                return back()
                    ->withErrors([
                        'builder_content' =>
                            'Valeur autoplay invalide.',
                    ])
                    ->withInput();
            }

            // Conserver le format utilisé
            // par le WebStudio historique.
            $safe['autoplay'] = in_array(
                $autoplay,
                $trueValues,
                true
            ) ? 'oui' : 'non';

            /**
             * 4. Vérifier les URLs.
             */
            foreach (self::URL_FIELDS as $field) {

                if (
                    $safe[$field] !== '' &&
                    !$this->safeUrl($safe[$field])
                ) {
                    return back()
                        ->withErrors([
                            'builder_content' =>
                                'URL invalide : ' . $field,
                        ])
                        ->withInput();
                }
            }

            /**
             * 5. Vérifier les couleurs.
             */
            foreach (self::COLOR_FIELDS as $field) {

                if (
                    $safe[$field] !== '' &&
                    !preg_match(
                        '/^#[0-9a-fA-F]{6}$/',
                        $safe[$field]
                    )
                ) {
                    return back()
                        ->withErrors([
                            'builder_content' =>
                                'Couleur invalide : ' . $field,
                        ])
                        ->withInput();
                }
            }

            /**
             * 6. Vérifier les animations.
             */
            $allowedAnimations = [
                '',
                'none',
                'fade',
                'fade-up',
                'slide-up',
                'slide-left',
                'slide-right',
                'zoom',
                'zoom-out',
                'rotate',
            ];

            if (
                !in_array(
                    $safe['animation'],
                    $allowedAnimations,
                    true
                )
            ) {
                return back()
                    ->withErrors([
                        'builder_content' =>
                            'Animation invalide.',
                    ])
                    ->withInput();
            }

            foreach (['duration' => '700', 'delay' => '0'] as $key => $default) {
                if ($safe[$key] === '') $safe[$key] = $default;
            }

            foreach (['parallax' => 'non', 'hover' => 'oui'] as $key => $default) {
                $value = $data[$key] ?? $default;
                if (!in_array($value, [true, false, 1, 0, '1', '0', 'true', 'false', 'oui', 'non', 'on', 'off'], true)) {
                    return back()->withErrors(['builder_content' => 'Option invalide : ' . $key])->withInput();
                }
                $safe[$key] = in_array($value, [true, 1, '1', 'true', 'oui', 'on'], true) ? 'oui' : 'non';
            }

            /**
             * 7. Conserver le bloc nettoyé.
             */
            $clean[] = [
                'type' => $block['type'],
                'data' => $safe,
            ];
        }

        /**
         * 8. Enregistrement.
         */
        $model->builder_content = $clean;
        $model->status = $validated['status'];

        $model->published_at =
            $validated['status'] === 'published'
                ? ($model->published_at ?? now())
                : null;

        $model->save();

        return redirect()
            ->route(
                'company.website.builder.edit',
                $model->id
            )
            ->with(
                'success',
                'Page enregistrée avec succès.'
            );
    }

    /**
     * Sécuriser les URLs.
     *
     * Autorise :
     * /contact
     * /storage/website/1/image.webp
     * https://exemple.com/image.jpg
     *
     * Refuse notamment :
     * javascript:
     * data:
     * file:
     * URLs avec identifiants intégrés
     */
    private function safeUrl(string $value): bool
{
    if ($value === '') {
        return false;
    }

    // Refuser les caractères de contrôle et les espaces.
    if (preg_match('/[[:cntrl:][:space:]]/', $value)) {
        return false;
    }

    // Refuser les caractères dangereux dans les URLs.
    if (strpbrk($value, '<>"\'`\\') !== false) {
        return false;
    }

    // Accepter les chemins locaux.
    if (str_starts_with($value, '/')) {
        return !str_starts_with($value, '//');
    }

    // Accepter uniquement HTTP et HTTPS.
    $parts = parse_url($value);

    if (!is_array($parts)) {
        return false;
    }

    return in_array(
        strtolower($parts['scheme'] ?? ''),
        ['http', 'https'],
        true
    )
    && !empty($parts['host'])
    && !isset($parts['user'])
    && !isset($parts['pass']);
}

}
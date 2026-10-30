<?php
namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

class WebsiteHtml
{
    public static function clean(?string $html): string
    {
        $config = HTMLPurifier_Config::createDefault();

        /*
        |--------------------------------------------------------------------------
        | Balises et attributs autorisés
        |--------------------------------------------------------------------------
        */

        $config->set(
            'HTML.Allowed',
            implode(',', [
                'p[class]',
                'br',
                'h2[class]',
                'h3[class]',
                'strong',
                'em',
                'u',
                'ul',
                'ol',
                'li',
                'blockquote',
                'a[href|title|target|class]',
                'img[src|alt|title|class|width|height]',
                'div[class]',
                'span[class]',
            ])
        );

        /*
        |--------------------------------------------------------------------------
        | Protocoles autorisés
        |--------------------------------------------------------------------------
        */

        $config->set('URI.AllowedSchemes', [
            'http'   => true,
            'https'  => true,
            'mailto' => true,
            'tel'    => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Sécurité des liens
        |--------------------------------------------------------------------------
        */

        $config->set('Attr.AllowedFrameTargets', [
            '_blank',
        ]);

        $config->set('HTML.Nofollow', true);
        $config->set('HTML.TargetBlank', true);

        /*
        |--------------------------------------------------------------------------
        | Nettoyage HTML
        |--------------------------------------------------------------------------
        */

        $config->set('AutoFormat.RemoveEmpty', true);

        /*
        |--------------------------------------------------------------------------
        | Protection contre les éléments dangereux
        |--------------------------------------------------------------------------
        |
        | HTMLPurifier supprimera notamment :
        | - Les scripts JavaScript
        | - Les événements onclick, onerror, etc.
        | - Les URL javascript:
        | - Les balises non autorisées
        |
        */

        $purifier = new HTMLPurifier($config);

        return $purifier->purify($html ?? '');
    }
}

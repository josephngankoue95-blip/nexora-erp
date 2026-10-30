<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlatformSettingController extends Controller
{
    /**
     * Afficher les paramètres de la plateforme.
     */
    public function index()
    {
        $settings = [

            /*
            |--------------------------------------------------------------------------
            | Général
            |--------------------------------------------------------------------------
            */

            'platform_name' => PlatformSetting::get(
                'platform_name',
                'ERP SaaS'
            ),

            'platform_slogan' => PlatformSetting::get(
                'platform_slogan',
                'Votre gestion simplifiée'
            ),

            'admin_email' => PlatformSetting::get(
                'admin_email',
                ''
            ),

            'admin_phone' => PlatformSetting::get(
                'admin_phone',
                ''
            ),

            'address' => PlatformSetting::get(
                'address',
                ''
            ),

            'city' => PlatformSetting::get(
                'city',
                ''
            ),

            'country' => PlatformSetting::get(
                'country',
                'Cameroun'
            ),

            'currency' => PlatformSetting::get(
                'currency',
                'XAF'
            ),

            'currency_symbol' => PlatformSetting::get(
                'currency_symbol',
                'FCFA'
            ),

            'platform_logo' => PlatformSetting::get(
                'platform_logo'
            ),


            /*
            |--------------------------------------------------------------------------
            | Apparence
            |--------------------------------------------------------------------------
            */

            'primary_color' => PlatformSetting::get(
                'primary_color',
                '#2563eb'
            ),

            'secondary_color' => PlatformSetting::get(
                'secondary_color',
                '#111827'
            ),

            'sidebar_color' => PlatformSetting::get(
                'sidebar_color',
                '#111827'
            ),

            'sidebar_text_color' => PlatformSetting::get(
                'sidebar_text_color',
                '#ffffff'
            ),

            'theme_mode' => PlatformSetting::get(
                'theme_mode',
                'light'
            ),

            'favicon' => PlatformSetting::get(
                'favicon'
            ),
        ];

        return view(
            'superadmin.settings.index',
            compact('settings')
        );
    }


    /**
     * Enregistrer les paramètres.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Général
            |--------------------------------------------------------------------------
            */

            'platform_name' => [
                'required',
                'string',
                'max:150',
            ],

            'platform_slogan' => [
                'nullable',
                'string',
                'max:255',
            ],

            'admin_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'admin_phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'address' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'country' => [
                'nullable',
                'string',
                'max:100',
            ],

            'currency' => [
                'required',
                'string',
                'max:10',
            ],

            'currency_symbol' => [
                'required',
                'string',
                'max:20',
            ],

            'platform_logo' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,svg',
                'max:2048',
            ],


            /*
            |--------------------------------------------------------------------------
            | Apparence
            |--------------------------------------------------------------------------
            */

            'primary_color' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'secondary_color' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'sidebar_color' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'sidebar_text_color' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'theme_mode' => [
                'required',
                'in:light,dark,system',
            ],

            'favicon' => [
                'nullable',
                'file',
                'mimes:png,ico,jpg,jpeg',
                'max:1024',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Paramètres généraux
        |--------------------------------------------------------------------------
        */

        PlatformSetting::set(
            'platform_name',
            $validated['platform_name']
        );

        PlatformSetting::set(
            'platform_slogan',
            $validated['platform_slogan'] ?? ''
        );

        PlatformSetting::set(
            'admin_email',
            $validated['admin_email'] ?? ''
        );

        PlatformSetting::set(
            'admin_phone',
            $validated['admin_phone'] ?? ''
        );

        PlatformSetting::set(
            'address',
            $validated['address'] ?? ''
        );

        PlatformSetting::set(
            'city',
            $validated['city'] ?? ''
        );

        PlatformSetting::set(
            'country',
            $validated['country'] ?? 'Cameroun'
        );

        PlatformSetting::set(
            'currency',
            $validated['currency']
        );

        PlatformSetting::set(
            'currency_symbol',
            $validated['currency_symbol']
        );


        /*
        |--------------------------------------------------------------------------
        | Apparence
        |--------------------------------------------------------------------------
        */

        PlatformSetting::set(
            'primary_color',
            $validated['primary_color']
        );

        PlatformSetting::set(
            'secondary_color',
            $validated['secondary_color']
        );

        PlatformSetting::set(
            'sidebar_color',
            $validated['sidebar_color']
        );

        PlatformSetting::set(
            'sidebar_text_color',
            $validated['sidebar_text_color']
        );

        PlatformSetting::set(
            'theme_mode',
            $validated['theme_mode']
        );


        /*
        |--------------------------------------------------------------------------
        | Logo plateforme
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('platform_logo')) {

            $oldLogo = PlatformSetting::get(
                'platform_logo'
            );

            if ($oldLogo) {
                Storage::disk('public')->delete(
                    $oldLogo
                );
            }

            $logoPath = $request
                ->file('platform_logo')
                ->store(
                    'platform',
                    'public'
                );

            PlatformSetting::set(
                'platform_logo',
                $logoPath
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Favicon
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('favicon')) {

            $oldFavicon = PlatformSetting::get(
                'favicon'
            );

            if ($oldFavicon) {
                Storage::disk('public')->delete(
                    $oldFavicon
                );
            }

            $faviconPath = $request
                ->file('favicon')
                ->store(
                    'platform',
                    'public'
                );

            PlatformSetting::set(
                'favicon',
                $faviconPath
            );
        }


        return back()->with(
            'success',
            'Les paramètres de la plateforme ont été enregistrés avec succès.'
        );
    }


    /**
     * Supprimer le logo.
     */
    public function removeLogo()
    {
        $logo = PlatformSetting::get(
            'platform_logo'
        );

        if ($logo) {
            Storage::disk('public')->delete(
                $logo
            );
        }

        PlatformSetting::set(
            'platform_logo',
            null
        );

        return back()->with(
            'success',
            'Le logo de la plateforme a été supprimé.'
        );
    }


    /**
     * Supprimer le favicon.
     */
    public function removeFavicon()
    {
        $favicon = PlatformSetting::get(
            'favicon'
        );

        if ($favicon) {
            Storage::disk('public')->delete(
                $favicon
            );
        }

        PlatformSetting::set(
            'favicon',
            null
        );

        return back()->with(
            'success',
            'Le favicon de la plateforme a été supprimé.'
        );
    }
}
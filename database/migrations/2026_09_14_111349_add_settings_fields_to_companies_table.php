<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | IDENTITÉ DE L'ENTREPRISE
            |--------------------------------------------------------------------------
            */

            $table->string('slogan')
                ->nullable()
                ->after('logo');


            /*
            |--------------------------------------------------------------------------
            | CONTACT
            |--------------------------------------------------------------------------
            */

            $table->string('email')
                ->nullable()
                ->change();

            $table->string('phone')
                ->nullable()
                ->change();

            $table->string('whatsapp')
                ->nullable();

            $table->string('website')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | LOCALISATION
            |--------------------------------------------------------------------------
            */

            $table->text('address')
                ->nullable()
                ->change();

            $table->string('city')
                ->nullable()
                ->change();

            $table->string('country')
                ->nullable()
                ->change();


            /*
            |--------------------------------------------------------------------------
            | INFORMATIONS LÉGALES
            |--------------------------------------------------------------------------
            */

            $table->string('tax_number')
                ->nullable();

            $table->string('rccm')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | CONFIGURATION COMMERCIALE
            |--------------------------------------------------------------------------
            */

            $table->string('currency')
                ->default('XAF');

            $table->string('currency_symbol')
                ->default('FCFA');

            $table->string('invoice_prefix')
                ->default('FAC-');

            $table->text('invoice_footer')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | CONFIGURATION DU STOCK
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('stock_alert_threshold')
                ->default(5);


            /*
            |--------------------------------------------------------------------------
            | APPARENCE
            |--------------------------------------------------------------------------
            */

            $table->string('primary_color')
                ->default('#2563eb');

            $table->string('date_format')
                ->default('d/m/Y');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {

            $table->dropColumn([
                'slogan',
                'whatsapp',
                'website',
                'tax_number',
                'rccm',
                'currency',
                'currency_symbol',
                'invoice_prefix',
                'invoice_footer',
                'stock_alert_threshold',
                'primary_color',
                'date_format',
            ]);

        });
    }
};
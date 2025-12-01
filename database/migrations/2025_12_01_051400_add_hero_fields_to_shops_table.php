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
        Schema::table('shops', function (Blueprint $table) {
            $table->string('hero_title')->nullable()->after('banner');
            $table->string('hero_subtitle')->nullable()->after('hero_title');
            $table->string('hero_button_text')->default('Voir les produits')->after('hero_subtitle');
            $table->string('hero_button_link')->nullable()->after('hero_button_text');
            $table->string('hero_background', 500)->default('linear-gradient(135deg, #667eea 0%, #764ba2 100%)')->after('hero_button_link');
            $table->string('hero_text_color')->default('#ffffff')->after('hero_background');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title',
                'hero_subtitle', 
                'hero_button_text',
                'hero_button_link',
                'hero_background',
                'hero_text_color',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('review_images')) {
            Schema::create('review_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('review_id')->constrained('product_reviews')->onDelete('cascade');
                $table->string('image_path');
                $table->timestamps();
            });
        }

        // Add helpful votes to reviews
        Schema::table('product_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('product_reviews', 'helpful_count')) {
                $table->integer('helpful_count')->default(0);
            }
            if (!Schema::hasColumn('product_reviews', 'verified_purchase')) {
                $table->boolean('verified_purchase')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_images');
        
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropColumn(['helpful_count', 'verified_purchase']);
        });
    }
};

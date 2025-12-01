<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->boolean('notify_new_products')->default(true);
            $table->boolean('notify_promotions')->default(true);
            $table->timestamps();

            $table->unique(['shop_id', 'customer_id']);
        });

        // Add follower count to shops
        Schema::table('shops', function (Blueprint $table) {
            if (!Schema::hasColumn('shops', 'followers_count')) {
                $table->integer('followers_count')->default(0)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_followers');
        
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('followers_count');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon');
            $table->string('color');
            $table->text('description')->nullable();
            $table->json('requirements')->nullable(); // JSON criteria
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('vendor_badge_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained('vendor_badges')->onDelete('cascade');
            $table->timestamp('awarded_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['vendor_id', 'badge_id']);
        });

        // Add verification fields to vendors
        Schema::table('vendors', function (Blueprint $table) {
            if (!Schema::hasColumn('vendors', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('status');
            }
            if (!Schema::hasColumn('vendors', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('is_verified');
            }
            if (!Schema::hasColumn('vendors', 'total_sales')) {
                $table->decimal('total_sales', 15, 2)->default(0)->after('verified_at');
            }
            if (!Schema::hasColumn('vendors', 'total_orders')) {
                $table->integer('total_orders')->default(0)->after('total_sales');
            }
            if (!Schema::hasColumn('vendors', 'avg_rating')) {
                $table->decimal('avg_rating', 3, 2)->nullable()->after('total_orders');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_badge_assignments');
        Schema::dropIfExists('vendor_badges');
        
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'verified_at', 'total_sales', 'total_orders', 'avg_rating']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('status'); // pending, confirmed, processing, shipped, in_transit, delivered, cancelled
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable(); // DHL, UPS, FedEx, etc.
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
        });

        // Add tracking fields to orders table
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('status');
            }
            if (!Schema::hasColumn('orders', 'carrier')) {
                $table->string('carrier')->nullable()->after('tracking_number');
            }
            if (!Schema::hasColumn('orders', 'estimated_delivery')) {
                $table->date('estimated_delivery')->nullable()->after('carrier');
            }
            if (!Schema::hasColumn('orders', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('estimated_delivery');
            }
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_trackings');
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tracking_number', 'carrier', 'estimated_delivery', 'shipped_at', 'delivered_at']);
        });
    }
};

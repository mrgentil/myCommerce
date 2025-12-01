<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add points to customers
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'loyalty_points')) {
                $table->integer('loyalty_points')->default(0)->after('email');
            }
            if (!Schema::hasColumn('customers', 'total_points_earned')) {
                $table->integer('total_points_earned')->default(0)->after('loyalty_points');
            }
        });

        // Points transactions history
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->integer('points');
            $table->enum('type', ['earned', 'redeemed', 'expired', 'bonus', 'refund']);
            $table->string('description');
            $table->string('reference_type')->nullable(); // Order, Review, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->integer('balance_after');
            $table->timestamps();

            $table->index(['customer_id', 'created_at']);
        });

        // Loyalty rewards (what points can be exchanged for)
        Schema::create('loyalty_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('points_required');
            $table->enum('reward_type', ['discount_percent', 'discount_fixed', 'free_shipping', 'product']);
            $table->decimal('reward_value', 10, 2)->nullable();
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_rewards');
        Schema::dropIfExists('loyalty_transactions');
        
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['loyalty_points', 'total_points_earned']);
        });
    }
};

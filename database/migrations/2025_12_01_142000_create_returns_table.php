<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_detail_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['return', 'refund', 'exchange'])->default('return');
            $table->enum('reason', [
                'defective',
                'wrong_item',
                'not_as_described',
                'changed_mind',
                'too_late',
                'damaged',
                'other'
            ]);
            $table->text('description');
            $table->integer('quantity')->default(1);
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'shipped',
                'received',
                'refunded',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->text('vendor_response')->nullable();
            $table->string('return_tracking')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['vendor_id', 'status']);
        });

        // Return images
        Schema::create('order_return_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_return_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_return_images');
        Schema::dropIfExists('order_returns');
    }
};

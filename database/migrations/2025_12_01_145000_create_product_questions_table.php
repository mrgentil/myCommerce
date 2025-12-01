<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->boolean('is_public')->default(true);
            $table->integer('helpful_count')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
        });

        Schema::create('product_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('product_questions')->onDelete('cascade');
            $table->string('answerer_type'); // vendor, customer
            $table->unsignedBigInteger('answerer_id');
            $table->text('answer');
            $table->boolean('is_official')->default(false); // Vendor answer
            $table->integer('helpful_count')->default(0);
            $table->timestamps();

            $table->index(['question_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_answers');
        Schema::dropIfExists('product_questions');
    }
};

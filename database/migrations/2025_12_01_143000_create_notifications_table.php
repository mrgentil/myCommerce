<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('notifiable_type'); // Customer, Vendor
            $table->unsignedBigInteger('notifiable_id');
            $table->string('type'); // order, message, review, return, promotion, system
            $table->string('title');
            $table->text('message');
            $table->string('icon')->nullable();
            $table->string('action_url')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index('read_at');
        });

        // Email notification preferences
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->boolean('email_orders')->default(true);
            $table->boolean('email_messages')->default(true);
            $table->boolean('email_reviews')->default(true);
            $table->boolean('email_promotions')->default(false);
            $table->boolean('email_newsletter')->default(false);
            $table->boolean('push_enabled')->default(false);
            $table->timestamps();

            $table->unique(['notifiable_type', 'notifiable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('user_notifications');
    }
};

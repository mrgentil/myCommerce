<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update vendors table
        Schema::table('vendors', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->default(10.00)->after('status');
        });

        // Change enum values for vendor status
        DB::statement("ALTER TABLE vendors MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'active', 'inactive', 'banned') DEFAULT 'pending'");

        // Update shops table
        Schema::table('shops', function (Blueprint $table) {
            $table->string('banner')->nullable()->after('logo');
            $table->string('address')->nullable()->after('description');
            $table->string('phone')->nullable()->after('address');
        });

        // Change enum values for shop status  
        DB::statement("ALTER TABLE shops MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'active', 'inactive') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('commission_rate');
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['banner', 'address', 'phone']);
        });
    }
};

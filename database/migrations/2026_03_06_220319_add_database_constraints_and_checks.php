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
        // Modify properties table
        Schema::table('properties', function (Blueprint $table) {
            $table->string('name', 255)->change();
        });

        // Modify rooms table - add check constraints
        Schema::table('rooms', function (Blueprint $table) {
            $table->integer('floor')->min(1)->change();
        });

        // Modify invoices table - add soft deletes & check constraints
        Schema::table('invoices', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
            // Amount must be positive
            $table->decimal('amount', 12, 2)->min(0)->change();
        });

        // Modify leases table - add check constraints
        Schema::table('leases', function (Blueprint $table) {
            $table->decimal('deposit_amount', 12, 2)->min(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};

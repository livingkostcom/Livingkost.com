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
        // Add audit columns to invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('verified_at')->constrained('users')->onDelete('set null');
            $table->foreignId('verified_by')->nullable()->after('created_by')->constrained('users')->onDelete('set null');
        });

        // Note: Leases table already has created_by and updated_by fields (as strings)
        // They will be properly converted later if needed
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeignIdFor('created_by');
            $table->dropColumn('created_by');
            $table->dropForeignIdFor('verified_by');
            $table->dropColumn('verified_by');
        });

        Schema::table('leases', function (Blueprint $table) {
            $table->dropForeignIdFor('created_by');
            $table->dropColumn('created_by');
            $table->dropForeignIdFor('updated_by');
            $table->dropColumn('updated_by');
        });
    }
};

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
        // Add indexes for foreign keys (performance)
        Schema::table('room_types', function (Blueprint $table) {
            $table->index('property_id');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->index('room_type_id');
            // Unique constraint: room_number is unique per room_type
            $table->unique(['room_type_id', 'room_number']);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
        });

        Schema::table('leases', function (Blueprint $table) {
            $table->index('tenant_id');
            $table->index('room_id');
            $table->index('status');
            // Composite index for queries
            $table->index(['tenant_id', 'status']);
            // Unique: one active lease per tenant
            $table->unique('tenant_id')->where('status', 'active');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index('lease_id');
            $table->index('status');
            $table->index('verified_at');
            // Composite indexes for common queries
            $table->index(['lease_id', 'status']);
            $table->index(['status', 'verified_at']);
            // Unique: one invoice per lease per month
            $table->unique(['lease_id', 'month_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropIndex(['property_id']);
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex(['room_type_id']);
            $table->dropUnique(['room_type_id', 'room_number']);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('leases', function (Blueprint $table) {
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['room_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['tenant_id', 'status']);
            $table->dropUnique(['tenant_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['lease_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['verified_at']);
            $table->dropIndex(['lease_id', 'status']);
            $table->dropIndex(['status', 'verified_at']);
            $table->dropUnique(['lease_id', 'month_year']);
        });
    }
};

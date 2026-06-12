<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that carry a denormalized owner_id for multi-tenant scoping.
     */
    private array $tables = [
        'properties',
        'room_types',
        'rooms',
        'tenants',
        'leases',
        'invoices',
        'expenses',
        'announcements',
        'maintenance_requests',
        'settings',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasColumn($table, 'owner_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->foreignId('owner_id')
                        ->nullable()
                        ->after('id')
                        ->constrained('users')
                        ->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'owner_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropConstrainedForeignId('owner_id');
                });
            }
        }
    }
};

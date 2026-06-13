<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This was meant to be a partial unique (one ACTIVE lease per tenant),
        // but MySQL ignores the ->where() clause, so it became a plain unique on
        // tenant_id across ALL leases — including soft-deleted ones — blocking
        // re-leasing to the same tenant. Drop it; the "one active lease per
        // tenant" rule is enforced in the application layer instead.
        $exists = collect(DB::select("SHOW INDEX FROM leases WHERE Key_name = 'leases_tenant_id_unique'"))->isNotEmpty();

        if ($exists) {
            Schema::table('leases', function (Blueprint $table) {
                $table->dropUnique('leases_tenant_id_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->unique('tenant_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenant_registrations', function (Blueprint $table) {
            $table->decimal('dp_amount', 12, 2)->nullable()->after('note');
            // none | online (DOKU) | manual (transfer + proof)
            $table->string('dp_method')->nullable()->after('dp_amount');
            // unpaid | paid | submitted (manual proof uploaded, awaiting owner)
            $table->string('dp_status')->nullable()->after('dp_method');
            $table->string('dp_reference')->nullable()->after('dp_status');
            $table->string('dp_proof')->nullable()->after('dp_reference');
            $table->timestamp('dp_paid_at')->nullable()->after('dp_proof');
        });
    }

    public function down(): void
    {
        Schema::table('tenant_registrations', function (Blueprint $table) {
            $table->dropColumn(['dp_amount', 'dp_method', 'dp_status', 'dp_reference', 'dp_proof', 'dp_paid_at']);
        });
    }
};

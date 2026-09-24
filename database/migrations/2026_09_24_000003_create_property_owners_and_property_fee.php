<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Per-property Living Kost fee (the deal between the property's owners
        // and the platform). Deducted from rent before the owners' split.
        if (! Schema::hasColumn('properties', 'platform_fee_percent')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->decimal('platform_fee_percent', 5, 2)->default(0)->after('owner_id');
            });
        }

        // Co-owners of a property and their profit-share proportion (sums to 100).
        if (! Schema::hasTable('property_owners')) {
            Schema::create('property_owners', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_id')->constrained()->cascadeOnDelete();
                $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
                $table->decimal('share_percent', 5, 2)->default(100);
                $table->timestamps();
                $table->unique(['property_id', 'owner_id']);
            });
        }

        // Backfill: every existing property → its current owner at 100%, and the
        // property fee = that owner's current wallet fee (preserve behaviour).
        foreach (DB::table('properties')->get() as $prop) {
            $exists = DB::table('property_owners')->where('property_id', $prop->id)->exists();
            if (! $exists && $prop->owner_id) {
                DB::table('property_owners')->insert([
                    'property_id' => $prop->id,
                    'owner_id' => $prop->owner_id,
                    'share_percent' => 100,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $fee = DB::table('owner_wallets')->where('owner_id', $prop->owner_id)->value('platform_fee_percent');
            DB::table('properties')->where('id', $prop->id)->update(['platform_fee_percent' => (float) ($fee ?? 0)]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('property_owners');
        if (Schema::hasColumn('properties', 'platform_fee_percent')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropColumn('platform_fee_percent');
            });
        }
    }
};

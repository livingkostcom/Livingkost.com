<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Owner-provided Google Maps link for the exact location (detail page
            // "Lihat di Google Maps" uses this instead of an address search).
            $table->string('maps_url', 2000)->nullable()->after('badge_text');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('maps_url');
        });
    }
};

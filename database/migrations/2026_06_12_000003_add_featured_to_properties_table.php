<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('status');
            $table->string('featured_image')->nullable()->after('is_featured');
            $table->string('location_label')->nullable()->after('featured_image');
            $table->string('badge_text')->nullable()->after('location_label');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'featured_image', 'location_label', 'badge_text']);
        });
    }
};

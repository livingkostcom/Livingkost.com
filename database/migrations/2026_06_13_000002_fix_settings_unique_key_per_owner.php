<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Drop the old single-column unique index
            $table->dropUnique('settings_key_unique');

            // Add composite unique so each owner can have their own setting per key
            $table->unique(['key', 'owner_id'], 'settings_key_owner_unique');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('settings_key_owner_unique');
            $table->unique('key', 'settings_key_unique');
        });
    }
};

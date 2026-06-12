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
        Schema::table('tenants', function (Blueprint $table) {
            // Add new columns
            $table->string('name')->nullable()->after('id');
            $table->string('email')->unique()->nullable()->after('name');
            $table->string('ktp_photo')->nullable()->after('avatar');
            $table->string('created_by')->nullable()->after('updated_at');
            $table->string('updated_by')->nullable()->after('created_by');
            $table->softDeletes();

            // Make user_id nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['name', 'email', 'ktp_photo', 'created_by', 'updated_by', 'deleted_at']);
            $table->unsignedBigInteger('user_id')->change();
        });
    }
};

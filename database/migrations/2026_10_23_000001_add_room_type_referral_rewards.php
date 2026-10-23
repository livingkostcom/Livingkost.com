<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenant_registrations', function (Blueprint $t) {
            $t->json('referral_reward_rates')->nullable();
        });
        Schema::table('referral_rewards', function (Blueprint $t) {
            $t->unsignedBigInteger('room_type_id')->nullable();
            $t->string('room_type_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('referral_rewards', fn (Blueprint $t) => $t->dropColumn(['room_type_id', 'room_type_name']));
        Schema::table('tenant_registrations', fn (Blueprint $t) => $t->dropColumn('referral_reward_rates'));
    }
};

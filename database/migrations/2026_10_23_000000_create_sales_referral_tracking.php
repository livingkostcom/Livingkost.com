<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    public function up(): void
    {
        Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);
        Schema::create('sales_agents', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained('users')->restrictOnDelete();
            $t->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete();
            $t->string('code', 32)->unique();
            $t->boolean('active')->default(true);
            $t->timestamps();
            $t->index(['owner_id', 'active']);
        });
        Schema::table('tenant_registrations', function (Blueprint $t) {
            $t->foreignId('sales_agent_id')->nullable()->constrained('sales_agents')->restrictOnDelete();
            $t->decimal('referral_reward_amount', 12, 2)->default(0);
        });
        Schema::create('referral_rewards', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->constrained('users')->restrictOnDelete();
            $t->foreignId('sales_agent_id')->constrained('sales_agents')->restrictOnDelete();
            $t->foreignId('registration_id')->unique()->constrained('tenant_registrations')->restrictOnDelete();
            $t->foreignId('tenant_id')->unique()->constrained('tenants')->restrictOnDelete();
            $t->foreignId('lease_id')->unique()->constrained('leases')->restrictOnDelete();
            $t->decimal('amount', 12, 2);
            $t->decimal('rent_amount', 12, 2);
            $t->timestamp('earned_at')->nullable();
            $t->timestamp('paid_at')->nullable();
            $t->foreignId('paid_by')->nullable()->constrained('users')->restrictOnDelete();
            $t->string('payment_reference')->nullable();
            $t->timestamps();
            $t->index(['owner_id', 'sales_agent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_rewards');
        Schema::table('tenant_registrations', function (Blueprint $t) {
            $t->dropConstrainedForeignId('sales_agent_id');
            $t->dropColumn('referral_reward_amount');
        });
        Schema::dropIfExists('sales_agents');
    }
};

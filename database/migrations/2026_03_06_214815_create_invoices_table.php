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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('leases')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('month_year')->comment('Format: YYYY-MM');
            $table->enum('status', ['unpaid', 'pending', 'paid'])->default('unpaid');
            $table->string('reference_number')->unique()->nullable();
            $table->date('due_date');
            $table->string('proof_of_payment')->nullable()->comment('Path to payment proof file');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

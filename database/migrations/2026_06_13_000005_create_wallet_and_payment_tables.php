<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Per-owner wallet + online-payment partnership config (super-admin controlled)
        Schema::create('owner_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->boolean('online_payment_enabled')->default(false);
            $table->decimal('platform_fee_percent', 5, 2)->default(0); // platform commission, 0 = none
            $table->decimal('balance', 12, 2)->default(0);          // current withdrawable balance
            $table->decimal('total_earned', 12, 2)->default(0);     // lifetime credited
            $table->decimal('total_disbursed', 12, 2)->default(0);  // lifetime disbursed
            $table->timestamps();
        });

        // Ledger of every credit (payment) / debit (disbursement) on a wallet
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['credit', 'debit']);
            $table->enum('source', ['payment', 'disbursement', 'adjustment'])->default('payment');
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->unsignedBigInteger('disbursement_id')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Manual disbursements from platform to owner's bank account, status-tracked
        Schema::create('disbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'rejected'])->default('pending');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->string('reference')->nullable(); // bukti transfer / no. referensi
            $table->text('notes')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // One row per online-payment attempt against an invoice (DOKU)
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('gateway')->default('doku');
            $table->string('reference')->unique();      // our invoice number sent to DOKU
            $table->string('external_id')->nullable();  // DOKU's transaction id
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'paid', 'failed', 'expired'])->default('pending');
            $table->text('payment_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('raw')->nullable();            // last gateway payload for audit
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('disbursements');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('owner_wallets');
    }
};

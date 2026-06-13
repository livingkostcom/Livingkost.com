<?php

namespace App\Models;

use App\Mail\ReceiptSent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Mail;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, \App\Models\Concerns\BelongsToOwner;

    protected $fillable = ['owner_id', 'lease_id', 'amount', 'month_year', 'status', 'reference_number', 'due_date', 'proof_of_payment', 'verified_at', 'created_by', 'verified_by'];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'verified_at' => 'datetime',
    ];

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    /**
     * WhatsApp-formatted bank transfer block for this invoice's owner.
     * Returns an empty string when the owner has no account number set.
     */
    public function bankTransferWaBlock(): string
    {
        $ownerId = $this->owner_id ?? $this->lease?->tenant?->owner_id ?? null;

        $number = Setting::getForOwner('bank_account_number', $ownerId);
        if (! $number) {
            return '';
        }

        $name = Setting::getForOwner('bank_name', $ownerId);
        $holder = Setting::getForOwner('bank_account_holder', $ownerId);

        $block = "Transfer ke:\n";
        if ($name) {
            $block .= "Bank: {$name}\n";
        }
        $block .= "No. Rek: {$number}";
        if ($holder) {
            $block .= "\na.n. {$holder}";
        }

        return $block;
    }

    /**
     * Whether this invoice's owner has DOKU online payment enabled.
     */
    public function isOnlinePaymentEnabled(): bool
    {
        $ownerId = $this->owner_id ?? $this->lease?->tenant?->owner_id ?? null;

        return \App\Models\OwnerWallet::onlineEnabledFor($ownerId);
    }

    /**
     * Deep link that initiates the DOKU payment for this invoice.
     */
    public function payUrl(): string
    {
        return 'https://www.livingkost.com/invoices/' . $this->id . '/pay';
    }

    /**
     * WhatsApp action block for reminders: a direct DOKU pay link when online
     * payment is enabled; otherwise bank transfer details + upload-proof link.
     */
    public function reminderActionWaBlock(): string
    {
        if ($this->isOnlinePaymentEnabled()) {
            return "\n\nBayar online sekarang di sini:\n" . $this->payUrl();
        }

        $bank = $this->bankTransferWaBlock();
        $bankBlock = $bank ? "\n\n{$bank}" : '';

        return $bankBlock . "\n\nSudah bayar? Unggah bukti pembayaran di sini:\nhttps://www.livingkost.com/tenant/invoices";
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Ymd');
        // Reference numbers are globally unique, so look across ALL owners
        // (bypass the owner global scope) to avoid colliding sequences.
        $lastInvoice = static::withoutGlobalScopes()
            ->where('reference_number', 'like', $prefix . '%')
            ->orderBy('reference_number', 'desc')
            ->first();

        $sequence = 1;
        if ($lastInvoice) {
            $lastSequence = (int) substr($lastInvoice->reference_number, -4);
            $sequence = $lastSequence + 1;
        }

        return $prefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function generateReceipt($userId): Receipt
    {
        // Delete existing receipt if any
        $this->receipt?->delete();

        // Generate unique receipt number
        $receiptNumber = 'RCP-' . date('Ymd') . '-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);

        // Create pdf directory if not exists
        $receiptDir = storage_path('app/receipts');
        if (!file_exists($receiptDir)) {
            mkdir($receiptDir, 0755, true);
        }

        // Create receipt record first
        $receipt = $this->receipt()->create([
            'receipt_number' => $receiptNumber,
            'pdf_path' => 'receipts/' . $receiptNumber . '.pdf',
            'issued_at' => now(),
            'created_by' => $userId,
        ]);

        // Generate PDF with receipt object
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('receipts.pdf', ['receipt' => $receipt]);
        $pdf->save(storage_path('app/receipts/' . $receiptNumber . '.pdf'));

        // Send email to tenant
        try {
            $tenantEmail = $this->lease->tenant->email;
            Mail::to($tenantEmail)->send(new ReceiptSent($receipt));
        } catch (\Exception $e) {
            // Log email error but don't fail receipt generation
            \Log::warning('Failed to send receipt email: ' . $e->getMessage());
        }

        return $receipt;
    }
}

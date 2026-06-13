<?php

namespace App\Livewire\Tenant\Invoice;

use App\Models\Invoice;
use App\Models\OwnerWallet;
use App\Models\PaymentTransaction;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\PaymentProofUploadedNotification;
use App\Services\DokuService;
use App\Services\WhatsAppService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TenantInvoiceIndex extends Component
{
    use WithPagination, WithFileUploads;

    /**
     * Component properties
     */
    public $search = '';
    public $filterStatus = '';
    public $showUploadModal = false;
    public $uploadingInvoiceId = null;
    public $uploadingAmount = null;
    public $uploadingRef = '';
    public $proofFile = null;
    public $successMessage = '';
    public $errorMessage = '';

    /**
     * Query string tracking
     */
    protected $queryString = ['search', 'filterStatus'];

    /**
     * Validation rules
     */
    protected $rules = [
        'proofFile' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
    ];

    /**
     * Property validation messages
     */
    protected $messages = [
        'proofFile.required' => 'Pilih file bukti pembayaran terlebih dahulu',
        'proofFile.file' => 'File harus berupa dokumen yang valid',
        'proofFile.mimes' => 'File harus berformat PDF, JPG, atau PNG',
        'proofFile.max' => 'Ukuran file tidak boleh lebih dari 5MB',
    ];

    /**
     * Updated search - reset pagination
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Updated filter - reset pagination
     */
    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    /**
     * Clear success message
     */
    public function clearMessages()
    {
        $this->successMessage = '';
        $this->errorMessage = '';
    }

    /**
     * Open upload modal
     */
    public function openUploadModal($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);

        // Verify ownership
        if (!Auth::user()->tenant || !Auth::user()->tenant->leases->pluck('id')->contains($invoice->lease_id)) {
            $this->errorMessage = 'Invoice ini bukan milik Anda';
            return;
        }

        // Check if already verified
        if ($invoice->verified_at) {
            $this->errorMessage = 'Invoice sudah diverifikasi';
            return;
        }

        $this->uploadingInvoiceId = $invoiceId;
        $this->uploadingAmount = $invoice->amount;
        $this->uploadingRef = $invoice->reference_number;
        $this->proofFile = null;
        $this->showUploadModal = true;
    }

    /**
     * Close upload modal
     */
    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->uploadingInvoiceId = null;
        $this->proofFile = null;
        $this->resetErrorBag();
    }

    /**
     * Submit payment proof upload
     */
    public function submitProof()
    {
        // Validate file
        $this->validate();

        $invoice = Invoice::find($this->uploadingInvoiceId);

        // Verify ownership again
        if (!Auth::user()->tenant || !Auth::user()->tenant->leases->pluck('id')->contains($invoice->lease_id)) {
            $this->errorMessage = 'Akses ditolak';
            return;
        }

        try {
            // Store file
            $path = $this->proofFile->store('invoices', 'local');

            // Update invoice
            $invoice->update([
                'proof_of_payment' => $path,
                'status' => 'pending', // Change status to pending
            ]);

            // Notify this tenant's owner & managers (in-app + email + WhatsApp)
            $this->notifyAdmins($invoice);

            $this->successMessage = 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi dari manager.';
            $this->closeUploadModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan saat mengunggah file: ' . $e->getMessage();
        }
    }

    /**
     * Start an online (DOKU) payment for an invoice and redirect to the
     * hosted DOKU payment page. Only when the owner has online payment enabled.
     */
    public function payOnline($invoiceId)
    {
        $invoice = Invoice::with('lease.tenant')->find($invoiceId);

        if (!$invoice || !Auth::user()->tenant || !Auth::user()->tenant->leases->pluck('id')->contains($invoice->lease_id)) {
            $this->errorMessage = 'Invoice ini bukan milik Anda';
            return;
        }

        $res = \App\Services\PaymentInitiator::startForInvoice($invoice);

        if (empty($res['success'])) {
            $this->errorMessage = 'Gagal memulai pembayaran online: ' . ($res['error'] ?? 'silakan coba lagi');
            return;
        }

        return redirect()->away($res['url']);
    }

    /**
     * Notify the tenant's owner & managers about an uploaded payment proof,
     * via in-app + email (best-effort) and WhatsApp. Failures never block upload.
     */
    private function notifyAdmins(Invoice $invoice): void
    {
        $invoice->load(['lease.tenant', 'lease.room']);
        $ownerId = $invoice->lease->tenant->owner_id;

        $admins = User::role(['owner', 'manager'])
            ->where(fn ($q) => $q->where('id', $ownerId)->orWhere('owner_id', $ownerId))
            ->get();

        foreach ($admins as $admin) {
            try {
                $admin->notify(new PaymentProofUploadedNotification($invoice));
            } catch (\Throwable $e) {
                Log::error('Payment proof notification failed', ['admin_id' => $admin->id, 'error' => $e->getMessage()]);
            }
        }

        // WhatsApp: owner's business number + each manager's own phone (deduped)
        $waMessage = $this->buildProofWaMessage($invoice);
        $sentTo = [];

        $ownerPhone = Setting::getValue('app_phone');
        if ($ownerPhone) {
            WhatsAppService::send($ownerPhone, $waMessage);
            $sentTo[] = preg_replace('/[^0-9]/', '', $ownerPhone);
        }

        foreach ($admins as $admin) {
            $phone = $admin->phone ?? null;
            if ($phone && !in_array(preg_replace('/[^0-9]/', '', $phone), $sentTo, true)) {
                WhatsAppService::send($phone, $waMessage);
                $sentTo[] = preg_replace('/[^0-9]/', '', $phone);
            }
        }
    }

    private function buildProofWaMessage(Invoice $invoice): string
    {
        $tenant = $invoice->lease->tenant->display_name ?? $invoice->lease->tenant->name;
        $room = $invoice->lease->room->room_number ?? '-';
        $amount = 'Rp ' . number_format($invoice->amount, 0, ',', '.');

        return "*Bukti Pembayaran Baru*\n\n"
            . "No. Invoice: {$invoice->reference_number}\n"
            . "Penghuni: {$tenant}\n"
            . "Kamar: {$room}\n"
            . "Jumlah: {$amount}\n\n"
            . "Status: Menunggu verifikasi.\n\n"
            . "Cek & verifikasi buktinya di sini:\n"
            . "https://livingkost.com/payment-verifications\n\n"
            . "_Living Kost_";
    }

    /**
     * Get invoices for current tenant
     */
    public function getInvoices()
    {
        $tenantId = Auth::user()->tenant?->id;

        if (!$tenantId) {
            return Invoice::whereRaw('1=0')->paginate(10); // Return empty
        }

        $query = Invoice::whereHas('lease.tenant', function ($q) use ($tenantId) {
            $q->where('tenants.id', $tenantId);
        })
        ->with(['lease.tenant', 'lease.room.roomType', 'creator', 'verifier'])
        ->orderBy('created_at', 'desc');

        // Search
        if ($this->search) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', $search)
                  ->orWhere('month_year', 'like', $search);
            });
        }

        // Filter by status
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        return $query->paginate(10);
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.tenant.invoice.tenant-invoice-index', [
            'invoices' => $this->getInvoices(),
            'statusMap' => [
                'unpaid' => ['label' => 'Belum Bayar', 'color' => 'red'],
                'pending' => ['label' => 'Menunggu Verifikasi', 'color' => 'yellow'],
                'paid' => ['label' => 'Sudah Bayar', 'color' => 'green'],
            ],
            // Owner's registered bank account (owner-scoped via the tenant's owner_id)
            'bank' => [
                'name' => Setting::getValue('bank_name'),
                'number' => Setting::getValue('bank_account_number'),
                'holder' => Setting::getValue('bank_account_holder'),
                'instructions' => Setting::getValue('payment_instructions'),
            ],
            // Whether this tenant's owner has DOKU online payment enabled
            'onlineEnabled' => OwnerWallet::onlineEnabledFor(Auth::user()->tenant?->owner_id),
        ]);
    }
}

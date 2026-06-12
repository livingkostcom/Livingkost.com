<?php

namespace App\Livewire\Tenant\Invoice;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

            $this->successMessage = 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi dari manager.';
            $this->closeUploadModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan saat mengunggah file: ' . $e->getMessage();
        }
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
        ]);
    }
}

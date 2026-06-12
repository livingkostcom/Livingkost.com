<?php

namespace App\Livewire\Admin\Payment;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

class PaymentVerificationIndex extends Component
{
    use WithPagination;

    /**
     * Component properties
     */
    #[Url]
    public $search = '';
    
    #[Url]
    public $filterStatus = 'pending';
    
    public $showDetailModal = false;
    public $showApprovalModal = false;
    public $showRejectionModal = false;
    public $viewingInvoiceId = null;
    public $approvingInvoiceId = null;
    public $rejectionReason = '';
    public $successMessage = '';
    public $errorMessage = '';

    /**
     * Validation rules
     */
    protected $rules = [
        'rejectionReason' => 'required|min:10|max:500',
    ];

    protected $messages = [
        'rejectionReason.required' => 'Alasan penolakan harus diisi',
        'rejectionReason.min' => 'Alasan penolakan minimal 10 karakter',
        'rejectionReason.max' => 'Alasan penolakan maksimal 500 karakter',
    ];

    /**
     * Mount - check authorization
     */
    public function mount()
    {
        if (!Auth::check()) {
            redirect('/login');
        }

        if (!Auth::user()->can('verify-payment')) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini');
        }
    }

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
     * Clear messages
     */
    public function clearMessages()
    {
        $this->successMessage = '';
        $this->errorMessage = '';
    }

    /**
     * Open detail modal
     */
    public function openDetailModal($invoiceId)
    {
        $this->viewingInvoiceId = $invoiceId;
        $this->showDetailModal = true;
    }

    /**
     * Close detail modal
     */
    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->viewingInvoiceId = null;
    }

    /**
     * Open approval modal
     */
    public function openApprovalModal($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);

        if (!$invoice) {
            $this->errorMessage = 'Invoice tidak ditemukan';
            return;
        }

        if ($invoice->status !== 'pending') {
            $this->errorMessage = 'Invoice harus dalam status "Menunggu Verifikasi"';
            return;
        }

        if (!$invoice->proof_of_payment) {
            $this->errorMessage = 'Tidak ada bukti pembayaran yang di-upload';
            return;
        }

        $this->approvingInvoiceId = $invoiceId;
        $this->showApprovalModal = true;
    }

    /**
     * Close approval modal
     */
    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
        $this->approvingInvoiceId = null;
    }

    /**
     * Approve payment
     */
    public function approvePayment()
    {
        $invoice = Invoice::find($this->approvingInvoiceId);

        if (!$invoice) {
            $this->errorMessage = 'Invoice tidak ditemukan';
            return;
        }

        try {
            $invoice->update([
                'status' => 'paid',
                'verified_at' => now(),
                'verified_by' => Auth::id(),
            ]);

            // Generate receipt
            $invoice->generateReceipt(Auth::id());

            $this->successMessage = "Invoice #{$invoice->reference_number} berhasil diverifikasi. Receipt telah digenerate dan akan dikirim ke tenant.";
            $this->closeApprovalModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage();
        }
    }

    /**
     * Open rejection modal
     */
    public function openRejectionModal($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);

        if (!$invoice) {
            $this->errorMessage = 'Invoice tidak ditemukan';
            return;
        }

        if ($invoice->status !== 'pending') {
            $this->errorMessage = 'Invoice harus dalam status "Menunggu Verifikasi"';
            return;
        }

        $this->approvingInvoiceId = $invoiceId;
        $this->rejectionReason = '';
        $this->showRejectionModal = true;
    }

    /**
     * Close rejection modal
     */
    public function closeRejectionModal()
    {
        $this->showRejectionModal = false;
        $this->approvingInvoiceId = null;
        $this->rejectionReason = '';
        $this->resetErrorBag();
    }

    /**
     * Reject payment
     */
    public function rejectPayment()
    {
        $this->validate();

        $invoice = Invoice::find($this->approvingInvoiceId);

        if (!$invoice) {
            $this->errorMessage = 'Invoice tidak ditemukan';
            return;
        }

        try {
            // Store rejection reason in notes/memo field or create separate table
            // For now, we'll update proof_of_payment to indicate rejection
            $invoice->update([
                'status' => 'unpaid',
                'proof_of_payment' => null, // Clear the uploaded proof
            ]);

            $this->successMessage = "Invoice #{$invoice->reference_number} ditolak. Tenant dapat mengunggah bukti pembayaran kembali.";
            $this->closeRejectionModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage();
        }
    }

    /**
     * Get invoices pending verification
     */
    public function getInvoices()
    {
        $query = Invoice::with(['lease.tenant', 'lease.room.roomType', 'creator', 'verifier'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // Search
        if ($this->search) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', $search)
                  ->orWhereHas('lease.tenant', function ($q) use ($search) {
                      $q->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search);
                  });
            });
        }

        return $query->paginate(15)->appends([
            'filterStatus' => $this->filterStatus,
            'search' => $this->search,
        ]);
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.admin.payment.payment-verification-index', [
            'invoices' => $this->getInvoices(),
            'statusMap' => [
                'unpaid' => ['label' => 'Belum Bayar', 'color' => 'red'],
                'pending' => ['label' => 'Menunggu Verifikasi', 'color' => 'yellow'],
                'paid' => ['label' => 'Sudah Bayar', 'color' => 'green'],
            ],
        ]);
    }
}

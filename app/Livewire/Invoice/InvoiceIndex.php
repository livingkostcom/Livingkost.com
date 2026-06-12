<?php

namespace App\Livewire\Invoice;

use App\Models\Invoice;
use App\Models\Lease;
use App\Notifications\PaymentReminderNotification;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class InvoiceIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $showDetailModal = false;
    public $detailingInvoice;
    public bool $showDeleteModal = false;
    public ?Invoice $deletingInvoice = null;
    public string $successMessage = '';
    public string $errorMessage = '';

    // Generate Invoice properties
    public bool $showGenerateModal = false;
    public string $generateMonthYear = '';
    public array $generatePreview = [];
    public bool $showGeneratePreview = false;

    protected $queryString = ['search', 'filterStatus'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function openDetailModal($invoiceId)
    {
        $this->detailingInvoice = Invoice::with('lease.tenant', 'lease.room.roomType.property', 'creator', 'verifier')->find($invoiceId);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailingInvoice = null;
    }

    public function openDeleteModal($invoiceId)
    {
        try {
            $invoice = Invoice::find($invoiceId);
            
            // Check if invoice is verified
            if ($invoice->verified_at) {
                $this->errorMessage = 'Tidak dapat menghapus invoice yang sudah diverifikasi. Hubungi admin untuk bantuan lebih lanjut.';
                return;
            }
            
            $this->authorize('delete', $invoice);
            $this->deletingInvoice = $invoice;
            $this->showDeleteModal = true;
            $this->errorMessage = '';
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->errorMessage = 'Anda tidak memiliki izin untuk menghapus invoice ini.';
        }
    }

    public function confirmDelete()
    {
        if ($this->deletingInvoice) {
            try {
                $this->authorize('delete', $this->deletingInvoice);
                $this->deletingInvoice->delete();
                $this->successMessage = 'Invoice berhasil dihapus!';
                $this->closeDeleteModal();
                $this->resetPage();
            } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
                $this->errorMessage = 'Anda tidak memiliki izin untuk menghapus invoice ini.';
            }
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deletingInvoice = null;
    }

    // --- Generate Invoice Methods ---

    public function openGenerateModal()
    {
        $this->generateMonthYear = now()->format('Y-m');
        $this->generatePreview = [];
        $this->showGeneratePreview = false;
        $this->showGenerateModal = true;
    }

    public function closeGenerateModal()
    {
        $this->showGenerateModal = false;
        $this->generatePreview = [];
        $this->showGeneratePreview = false;
    }

    public function previewGenerate()
    {
        $this->validate(['generateMonthYear' => 'required|date_format:Y-m']);

        $targetMonth = Carbon::createFromFormat('Y-m', $this->generateMonthYear);

        $leases = Lease::where('status', 'active')
            ->where('start_date', '<=', $targetMonth->copy()->endOfMonth())
            ->where('end_date', '>=', $targetMonth->copy()->startOfMonth())
            ->with(['tenant', 'room.roomType'])
            ->get();

        $this->generatePreview = [];

        foreach ($leases as $lease) {
            $exists = Invoice::where('lease_id', $lease->id)
                ->where('month_year', $this->generateMonthYear)
                ->exists();

            $dueDay = min($lease->due_date_per_month, $targetMonth->daysInMonth);
            $dueDate = $targetMonth->copy()->day($dueDay)->format('Y-m-d');

            $this->generatePreview[] = [
                'lease_id' => $lease->id,
                'tenant_name' => $lease->tenant->display_name,
                'room_number' => $lease->room->room_number,
                'room_type' => $lease->room->roomType->name,
                'amount' => $lease->room->roomType->price,
                'due_date' => $dueDate,
                'already_exists' => $exists,
            ];
        }

        $this->showGeneratePreview = true;
    }

    public function generateInvoices()
    {
        $this->validate(['generateMonthYear' => 'required|date_format:Y-m']);

        $targetMonth = Carbon::createFromFormat('Y-m', $this->generateMonthYear);

        $leases = Lease::where('status', 'active')
            ->where('start_date', '<=', $targetMonth->copy()->endOfMonth())
            ->where('end_date', '>=', $targetMonth->copy()->startOfMonth())
            ->with('room.roomType')
            ->get();

        $created = 0;
        $skipped = 0;

        foreach ($leases as $lease) {
            $exists = Invoice::where('lease_id', $lease->id)
                ->where('month_year', $this->generateMonthYear)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $dueDay = min($lease->due_date_per_month, $targetMonth->daysInMonth);
            $dueDate = $targetMonth->copy()->day($dueDay);

            Invoice::create([
                'lease_id' => $lease->id,
                'amount' => $lease->room->roomType->price,
                'month_year' => $this->generateMonthYear,
                'status' => 'unpaid',
                'reference_number' => Invoice::generateInvoiceNumber(),
                'due_date' => $dueDate,
                'created_by' => Auth::id(),
            ]);

            $created++;
        }

        $this->closeGenerateModal();
        $this->successMessage = "{$created} invoice berhasil dibuat" . ($skipped > 0 ? ", {$skipped} dilewati (sudah ada)" : "");
    }

    public function getEligibleGenerateCountProperty(): int
    {
        return collect($this->generatePreview)->where('already_exists', false)->count();
    }

    // --- Payment Reminder Methods ---

    public function sendReminder(int $invoiceId)
    {
        $invoice = Invoice::with(['lease.tenant.user', 'lease.room.roomType'])->find($invoiceId);

        if (!$invoice || $invoice->status === 'paid') {
            $this->errorMessage = 'Invoice tidak ditemukan atau sudah lunas.';
            return;
        }

        $user = $invoice->lease->tenant->user;

        if (!$user) {
            $this->errorMessage = "Tenant '{$invoice->lease->tenant->display_name}' tidak terhubung dengan akun user.";
            return;
        }

        $today = now()->startOfDay();
        $reminderType = match (true) {
            $invoice->due_date->lt($today) => 'overdue',
            $invoice->due_date->eq($today) => 'due_today',
            default => 'upcoming',
        };

        $user->notify(new PaymentReminderNotification($invoice, $reminderType));
        $this->successMessage = "Pengingat berhasil dikirim ke {$invoice->lease->tenant->display_name}";
    }

    public function sendBulkReminders()
    {
        $today = now()->startOfDay();

        $invoices = Invoice::where('status', 'unpaid')
            ->where('due_date', '<=', $today->copy()->addDays(3))
            ->with(['lease.tenant.user', 'lease.room.roomType'])
            ->get();

        if ($invoices->isEmpty()) {
            $this->successMessage = 'Tidak ada invoice yang perlu dikirimi pengingat.';
            return;
        }

        $sent = 0;
        $skipped = 0;

        foreach ($invoices as $invoice) {
            $user = $invoice->lease->tenant->user;

            if (!$user) {
                $skipped++;
                continue;
            }

            $reminderType = match (true) {
                $invoice->due_date->lt($today) => 'overdue',
                $invoice->due_date->eq($today) => 'due_today',
                default => 'upcoming',
            };

            $user->notify(new PaymentReminderNotification($invoice, $reminderType));
            $sent++;
        }

        $this->successMessage = "{$sent} pengingat berhasil dikirim" . ($skipped > 0 ? ", {$skipped} dilewati (tanpa akun)" : "");
    }

    public function render()
    {
        $invoices = Invoice::query()
            ->with('lease.tenant', 'lease.room', 'creator', 'verifier')
            ->when($this->search, function ($query) {
                $query->whereHas('lease', function ($q) {
                    $q->whereHas('tenant', function ($tq) {
                        $tq->where('name', 'like', '%' . $this->search . '%')
                           ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
                })
                ->orWhere('reference_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.invoice.invoice-index', [
            'invoices' => $invoices,
        ]);
    }
}

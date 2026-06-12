<?php

namespace App\Livewire\Invoice;

use App\Models\Invoice;
use App\Models\Lease;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class InvoiceForm extends Component
{
    public $invoiceId;
    public $lease_id = '';
    public $amount = '';
    public $month_year = '';
    public $status = 'unpaid';
    public $due_date = '';
    public $notes = '';

    protected $rules = [
        'lease_id' => 'required|exists:leases,id',
        'amount' => 'required|numeric|min:0',
        'month_year' => 'required|date_format:Y-m',
        'due_date' => 'required|date|after_or_equal:today',
        'status' => 'required|in:unpaid,pending,paid',
    ];

    public function mount()
    {
        if ($this->invoiceId) {
            $invoice = Invoice::find($this->invoiceId);
            
            // Check authorization
            try {
                $this->authorize('update', $invoice);
            } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
                session()->flash('error', 'Anda tidak memiliki izin untuk mengubah invoice ini.');
                return redirect()->route('invoices.index');
            }
            
            // Check if invoice is already verified
            if ($invoice->verified_at) {
                session()->flash('error', 'Invoice yang sudah diverifikasi tidak dapat diubah. Hubungi admin untuk bantuan.');
                return redirect()->route('invoices.index');
            }
            
            $this->lease_id = $invoice->lease_id;
            $this->amount = $invoice->amount;
            $this->month_year = $invoice->month_year;
            $this->status = $invoice->status;
            $this->due_date = $invoice->due_date?->format('Y-m-d');
            $this->notes = $invoice->notes;
        } else {
            // Set default month_year to current month
            $this->month_year = now()->format('Y-m');
            // Set default due_date to 10th of next month
            $this->due_date = now()->addMonth()->format('Y-m-10');
        }
    }

    public function updatedLeaseId()
    {
        if ($this->lease_id) {
            $lease = Lease::with('room.roomType')->find($this->lease_id);
            if ($lease) {
                // Auto-set amount to room price per month
                $this->amount = $lease->room->roomType->price;
            }
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'lease_id' => $this->lease_id,
            'amount' => $this->amount,
            'month_year' => $this->month_year,
            'status' => $this->status,
            'due_date' => $this->due_date,
        ];

        if ($this->invoiceId) {
            $invoice = Invoice::find($this->invoiceId);
            $invoice->update($data);
            $message = 'Invoice berhasil diperbarui';
        } else {
            // Generate reference number
            $data['reference_number'] = Invoice::generateInvoiceNumber();
            $data['created_by'] = Auth::id();
            Invoice::create($data);
            $message = 'Invoice berhasil dibuat';
        }

        session()->flash('success', $message);
        return redirect()->route('invoices.index');
    }

    public function render()
    {
        // Get active leases that don't have invoices for current month
        $leases = Lease::with('tenant', 'room.roomType')
            ->where('status', 'active')
            ->get()
            ->filter(function ($lease) {
                // Filter: exclude leases that already have invoice for selected month
                if ($this->month_year) {
                    $existingInvoice = $lease->invoices()
                        ->where('month_year', $this->month_year)
                        ->where(function ($query) {
                            if ($this->invoiceId) {
                                $query->where('id', '!=', $this->invoiceId);
                            }
                        })
                        ->exists();
                    return !$existingInvoice;
                }
                return true;
            });

        return view('livewire.invoice.invoice-form', [
            'leases' => $leases,
        ]);
    }
}

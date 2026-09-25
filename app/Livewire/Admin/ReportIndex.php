<?php

namespace App\Livewire\Admin;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\OwnerWallet;
use App\Models\PaymentTransaction;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Url;

class ReportIndex extends Component
{
    #[Url]
    public string $tab = 'payment';

    #[Url]
    public string $monthYear = '';

    #[Url]
    public string $propertyFilter = '';

    public function mount()
    {
        if (!Auth::user()->can('view-reports')) {
            abort(403);
        }

        if (!$this->monthYear) {
            $this->monthYear = now()->format('Y-m');
        }
    }

    public function setTab(string $tab)
    {
        $this->tab = $tab;
    }

    /**
     * Eager-load spec for an invoice's related lease/tenant/room/property.
     * For co-owner viewers every related model carries its own owner global
     * scope, which would null it out for a co-owner, so we strip the scopes.
     */
    private function invoiceRelations(bool $coView): array
    {
        if (! $coView) {
            return ['lease.tenant.user', 'lease.room.roomType.property'];
        }

        return [
            'lease' => fn ($q) => $q->withoutGlobalScopes()->with([
                'tenant' => fn ($t) => $t->withoutGlobalScopes()->with(['user']),
                'room' => fn ($r) => $r->withoutGlobalScopes()->with([
                    'roomType' => fn ($rt) => $rt->withoutGlobalScopes()->with([
                        'property' => fn ($p) => $p->withoutGlobalScopes(),
                    ]),
                ]),
            ]),
        ];
    }

    /**
     * Rekap Pembayaran Bulanan
     */
    public function getPaymentRecap(): array
    {
        // Co-owner viewers get a read-only recap for their co-owned properties.
        $user = Auth::user();
        $coView = $user->isCoOwnerViewer();
        $coIds = $coView ? $user->coOwnedPropertyIds() : [];
        $leaseIds = $coView ? $user->coOwnedLeaseIds() : [];

        $query = ($coView
                ? Invoice::withoutGlobalScopes()->whereIn('lease_id', $leaseIds)
                : Invoice::query())
            ->where('month_year', $this->monthYear)
            ->with($this->invoiceRelations($coView));

        if ($this->propertyFilter) {
            $query->whereHas('lease.room.roomType.property', function ($q) {
                $q->where('id', $this->propertyFilter);
            });
        }

        $invoices = $query->orderBy('status')->get();

        $paid = $invoices->where('status', 'paid');
        $pending = $invoices->where('status', 'pending');
        $unpaid = $invoices->where('status', 'unpaid');

        $paidAmount = (float) $paid->sum('amount');

        // Net income for this month = collected − expenses − platform fee
        $monthStart = Carbon::createFromFormat('Y-m', $this->monthYear)->startOfMonth();
        $monthEnd = (clone $monthStart)->endOfMonth();
        $expenses = (float) ($coView
            ? Expense::withoutGlobalScopes()->whereIn('property_id', $coIds)
            : Expense::query())->whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount');

        $ownerId = Auth::user()->ownerId();
        $feePercent = (float) (OwnerWallet::where('owner_id', $ownerId)->value('platform_fee_percent') ?? 0);
        $onlineGross = $ownerId
            ? (float) PaymentTransaction::where('owner_id', $ownerId)
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('amount')
            : 0;
        $platformFee = round($onlineGross * $feePercent / 100, 2);

        return [
            'invoices' => $invoices,
            'total_amount' => $invoices->sum('amount'),
            'paid_amount' => $paidAmount,
            'expenses' => $expenses,
            'platform_fee' => $platformFee,
            'net_income' => $paidAmount - $expenses - $platformFee,
            'pending_amount' => $pending->sum('amount'),
            'unpaid_amount' => $unpaid->sum('amount'),
            'paid_count' => $paid->count(),
            'pending_count' => $pending->count(),
            'unpaid_count' => $unpaid->count(),
            'collection_rate' => $invoices->count() > 0
                ? round(($paid->count() / $invoices->count()) * 100, 1)
                : 0,
        ];
    }

    /**
     * Tingkat Hunian
     */
    public function getOccupancyReport(): array
    {
        $user = Auth::user();
        $coView = $user->isCoOwnerViewer();

        $properties = ($coView
            ? Property::withoutGlobalScopes()->whereIn('id', $user->coOwnedPropertyIds())
                ->with(['roomTypes' => fn ($q) => $q->withoutGlobalScopes()
                    ->with(['rooms' => fn ($r) => $r->withoutGlobalScopes()])])
            : Property::with(['roomTypes.rooms']))->get();

        $report = [];
        foreach ($properties as $property) {
            $rooms = $property->roomTypes->flatMap->rooms;
            $total = $rooms->count();
            $occupied = $rooms->where('status', 'occupied')->count();
            $available = $rooms->where('status', 'available')->count();
            $maintenance = $rooms->where('status', 'maintenance')->count();

            $report[] = [
                'property' => $property,
                'total_rooms' => $total,
                'occupied' => $occupied,
                'available' => $available,
                'maintenance' => $maintenance,
                'occupancy_rate' => $total > 0 ? round(($occupied / $total) * 100, 1) : 0,
            ];
        }

        $totalRooms = collect($report)->sum('total_rooms');
        $totalOccupied = collect($report)->sum('occupied');

        return [
            'properties' => $report,
            'total_rooms' => $totalRooms,
            'total_occupied' => $totalOccupied,
            'total_available' => collect($report)->sum('available'),
            'total_maintenance' => collect($report)->sum('maintenance'),
            'overall_rate' => $totalRooms > 0 ? round(($totalOccupied / $totalRooms) * 100, 1) : 0,
        ];
    }

    /**
     * Tagihan Belum Lunas
     */
    public function getOutstandingInvoices()
    {
        $user = Auth::user();
        $coView = $user->isCoOwnerViewer();

        $query = ($coView
                ? Invoice::withoutGlobalScopes()->whereIn('lease_id', $user->coOwnedLeaseIds())
                : Invoice::query())
            ->whereIn('status', ['unpaid', 'pending'])
            ->with($this->invoiceRelations($coView))
            ->orderBy('due_date', 'asc');

        if ($this->propertyFilter) {
            $query->whereHas('lease.room.roomType.property', function ($q) {
                $q->where('id', $this->propertyFilter);
            });
        }

        return $query->get();
    }

    /**
     * Riwayat Penyewa
     */
    public function getTenantReport(): array
    {
        $user = Auth::user();
        $coView = $user->isCoOwnerViewer();
        $leaseIds = $coView ? $user->coOwnedLeaseIds() : [];

        if ($coView) {
            $tenantIds = Lease::withoutGlobalScopes()->whereIn('id', $leaseIds)
                ->pluck('tenant_id')->unique()->all();
            $base = fn () => Tenant::withoutGlobalScopes()->whereIn('id', $tenantIds);
        } else {
            $base = fn () => Tenant::query();
        }

        $active = $base()->where('status', 'active')->count();
        $inactive = $base()->where('status', 'inactive')->count();
        $evicted = $base()->where('status', 'evicted')->count();

        $tenants = $base()->with(['user', 'leases' => function ($q) use ($coView, $leaseIds) {
            if ($coView) {
                $q->withoutGlobalScopes()->whereIn('id', $leaseIds)->with([
                    'room' => fn ($r) => $r->withoutGlobalScopes()->with([
                        'roomType' => fn ($rt) => $rt->withoutGlobalScopes()->with([
                            'property' => fn ($p) => $p->withoutGlobalScopes(),
                        ]),
                    ]),
                    'invoices' => fn ($i) => $i->withoutGlobalScopes(),
                ])->latest('start_date');
            } else {
                $q->with(['room.roomType.property', 'invoices'])->latest('start_date');
            }
        }])
            ->orderBy('status')
            ->get()
            ->map(function ($tenant) {
                $activeLease = $tenant->leases->where('status', 'active')->first();
                $totalInvoices = $tenant->leases->flatMap->invoices;
                $paidInvoices = $totalInvoices->where('status', 'paid');
                $unpaidInvoices = $totalInvoices->whereIn('status', ['unpaid', 'pending']);

                return [
                    'tenant' => $tenant,
                    'active_lease' => $activeLease,
                    'total_paid' => $paidInvoices->sum('amount'),
                    'total_outstanding' => $unpaidInvoices->sum('amount'),
                    'payment_rate' => $totalInvoices->count() > 0
                        ? round(($paidInvoices->count() / $totalInvoices->count()) * 100, 1)
                        : 0,
                    'lease_count' => $tenant->leases->count(),
                ];
            });

        return [
            'tenants' => $tenants,
            'active' => $active,
            'inactive' => $inactive,
            'evicted' => $evicted,
            'total' => $active + $inactive + $evicted,
        ];
    }

    public function exportPaymentPdf()
    {
        $recap = $this->getPaymentRecap();
        $monthLabel = Carbon::createFromFormat('Y-m', $this->monthYear)->translatedFormat('F Y');
        $propertyName = $this->propertyFilter ? Property::find($this->propertyFilter)?->name : null;

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reports.pdf.payment-recap', compact('recap', 'monthLabel', 'propertyName'));
        $pdf->setPaper('A4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'rekap-pembayaran-' . $this->monthYear . '.pdf');
    }

    public function exportOutstandingPdf()
    {
        $invoices = $this->getOutstandingInvoices();
        $propertyName = $this->propertyFilter ? Property::find($this->propertyFilter)?->name : null;

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reports.pdf.outstanding', compact('invoices', 'propertyName'));
        $pdf->setPaper('A4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'tagihan-belum-lunas-' . now()->format('Y-m-d') . '.pdf');
    }

    public function render()
    {
        $user = Auth::user();
        $properties = $user->isCoOwnerViewer()
            ? Property::withoutGlobalScopes()->whereIn('id', $user->coOwnedPropertyIds())
                ->orderBy('name')->get()
            : Property::orderBy('name')->get();

        $data = match ($this->tab) {
            'payment' => ['paymentRecap' => $this->getPaymentRecap()],
            'occupancy' => ['occupancyReport' => $this->getOccupancyReport()],
            'outstanding' => ['outstandingInvoices' => $this->getOutstandingInvoices()],
            'tenant' => ['tenantReport' => $this->getTenantReport()],
            default => ['paymentRecap' => $this->getPaymentRecap()],
        };

        return view('livewire.admin.report-index', array_merge($data, [
            'properties' => $properties,
        ]));
    }
}

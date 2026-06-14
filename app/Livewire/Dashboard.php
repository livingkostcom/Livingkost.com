<?php

namespace App\Livewire;

use App\Models\CompanyTransaction;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\OwnerWallet;
use App\Models\PaymentTransaction;
use App\Models\WalletTransaction;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function getOwnerMetrics(): array
    {
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        $currentMonth = now()->format('Y-m');
        $incomeThisMonth = Invoice::where('status', 'paid')
            ->whereYear('verified_at', now()->year)
            ->whereMonth('verified_at', now()->month)
            ->sum('amount');

        $overdueInvoices = Invoice::where('status', 'unpaid')
            ->where('due_date', '<', now())
            ->count();

        $pendingMaintenance = MaintenanceRequest::where('status', 'pending')->count();

        $expenseThisMonth = Expense::whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');

        // Platform fee charged on this month's online (DOKU) payments.
        $ownerId = Auth::user()->ownerId();
        $feePercent = (float) (OwnerWallet::where('owner_id', $ownerId)->value('platform_fee_percent') ?? 0);
        $onlineGrossThisMonth = $ownerId
            ? (float) PaymentTransaction::where('owner_id', $ownerId)
                ->where('status', 'paid')
                ->whereYear('paid_at', now()->year)
                ->whereMonth('paid_at', now()->month)
                ->sum('amount')
            : 0;
        $platformFeeThisMonth = round($onlineGrossThisMonth * $feePercent / 100, 2);

        $netIncomeThisMonth = (float) $incomeThisMonth - (float) $expenseThisMonth - $platformFeeThisMonth;

        return [
            'total_properties' => Property::count(),
            'total_rooms' => $totalRooms,
            'occupied_rooms' => $occupiedRooms,
            'available_rooms' => Room::where('status', 'available')->count(),
            'occupancy_rate' => $occupancyRate,
            'income_this_month' => $incomeThisMonth,
            'overdue_invoices' => $overdueInvoices,
            'pending_payments' => Invoice::where('status', 'pending')->count(),
            'pending_maintenance' => $pendingMaintenance,
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'active_leases' => Lease::where('status', 'active')->count(),
            'expense_this_month' => $expenseThisMonth,
            'platform_fee_this_month' => $platformFeeThisMonth,
            'net_income_this_month' => $netIncomeThisMonth,
        ];
    }

    public function getIncomeChartData(): array
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push([
                'label' => $date->translatedFormat('M Y'),
                'year' => $date->year,
                'month' => $date->month,
            ]);
        }

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $yearExpr = "CAST(strftime('%Y', verified_at) AS INTEGER)";
            $monthExpr = "CAST(strftime('%m', verified_at) AS INTEGER)";
        } else {
            $yearExpr = 'YEAR(verified_at)';
            $monthExpr = 'MONTH(verified_at)';
        }

        $incomeData = Invoice::where('status', 'paid')
            ->where('verified_at', '>=', now()->subMonths(5)->startOfMonth())
            ->select(
                DB::raw("{$yearExpr} as year"),
                DB::raw("{$monthExpr} as month"),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn($item) => $item->year . '-' . $item->month);

        $labels = [];
        $data = [];
        foreach ($months as $m) {
            $key = $m['year'] . '-' . $m['month'];
            $labels[] = $m['label'];
            $data[] = (float) ($incomeData[$key]->total ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function getRecentInvoices()
    {
        return Invoice::with(['lease.tenant', 'lease.room'])
            ->latest()
            ->take(5)
            ->get();
    }

    public function getRecentMaintenance()
    {
        return MaintenanceRequest::with(['tenant', 'room'])
            ->latest()
            ->take(5)
            ->get();
    }

    public function getTenantData(): array
    {
        $tenant = Auth::user()->tenant;
        if (!$tenant) {
            return ['lease' => null, 'unpaid' => 0, 'next_invoice' => null, 'maintenance' => collect()];
        }

        $lease = $tenant->leases()->where('status', 'active')->with('room.roomType')->first();

        $unpaidCount = Invoice::whereHas('lease', fn($q) => $q->where('tenant_id', $tenant->id))
            ->where('status', 'unpaid')
            ->count();

        $nextInvoice = Invoice::whereHas('lease', fn($q) => $q->where('tenant_id', $tenant->id))
            ->whereIn('status', ['unpaid', 'pending'])
            ->orderBy('due_date')
            ->first();

        $maintenance = MaintenanceRequest::where('tenant_id', $tenant->id)
            ->latest()
            ->take(3)
            ->get();

        return [
            'lease' => $lease,
            'unpaid' => $unpaidCount,
            'next_invoice' => $nextInvoice,
            'maintenance' => $maintenance,
        ];
    }

    /**
     * Platform-wide overview + per-owner breakdown for the super-admin.
     * Models bypass the owner global scope for super-admins, so unscoped
     * counts are platform-wide and explicit owner_id filters are exact.
     */
    public function getSuperAdminData(): array
    {
        $totalRooms = Room::count();
        $occupiedRooms = Lease::where('status', 'active')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        $incomeThisMonth = Invoice::where('status', 'paid')
            ->whereYear('verified_at', now()->year)
            ->whereMonth('verified_at', now()->month)
            ->sum('amount');

        $platform = [
            'total_owners' => User::role('owner')->count(),
            'total_managers' => User::role('manager')->count(),
            'total_tenants' => Tenant::where('status', 'active')->count(),
            'total_properties' => Property::count(),
            'total_rooms' => $totalRooms,
            'occupied_rooms' => $occupiedRooms,
            'occupancy_rate' => $occupancyRate,
            'income_this_month' => $incomeThisMonth,
            'pending_payments' => Invoice::where('status', 'pending')->count(),
            'overdue_invoices' => Invoice::where('status', 'unpaid')->where('due_date', '<', now())->count(),
            'pending_maintenance' => MaintenanceRequest::where('status', 'pending')->count(),
        ];

        // --- Platform's own net income (super-admin's earnings) this month ---
        // Platform fee = online payments collected − amounts credited to owners.
        $onlineGross = (float) PaymentTransaction::where('status', 'paid')
            ->whereYear('paid_at', now()->year)->whereMonth('paid_at', now()->month)
            ->sum('amount');
        $creditedToOwners = (float) WalletTransaction::where('type', 'credit')->where('source', 'payment')
            ->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)
            ->sum('amount');
        $feeIncome = round($onlineGross - $creditedToOwners, 2);

        $otherIncome = (float) CompanyTransaction::income()
            ->whereYear('transaction_date', now()->year)->whereMonth('transaction_date', now()->month)
            ->sum('amount');

        $companyExpense = (float) CompanyTransaction::expense()
            ->whereYear('transaction_date', now()->year)->whereMonth('transaction_date', now()->month)
            ->sum('amount');

        $platform['fee_income'] = $feeIncome;
        $platform['other_income'] = $otherIncome;
        $platform['company_expense'] = $companyExpense;
        $platform['net_income'] = $feeIncome + $otherIncome - $companyExpense;

        $rows = [];
        foreach (User::role('owner')->orderBy('name')->get() as $owner) {
            $oid = $owner->id;
            $rooms = Room::where('owner_id', $oid)->count();
            $occupied = Lease::where('owner_id', $oid)->where('status', 'active')->count();

            $rows[] = [
                'name' => $owner->name,
                'email' => $owner->email,
                'properties' => Property::where('owner_id', $oid)->count(),
                'rooms' => $rooms,
                'occupied' => $occupied,
                'occupancy' => $rooms > 0 ? round(($occupied / $rooms) * 100) : 0,
                'tenants' => Tenant::where('owner_id', $oid)->where('status', 'active')->count(),
                'income' => Invoice::where('owner_id', $oid)
                    ->where('status', 'paid')
                    ->whereYear('verified_at', now()->year)
                    ->whereMonth('verified_at', now()->month)
                    ->sum('amount'),
            ];
        }

        return ['platform' => $platform, 'owners' => $rows];
    }

    /**
     * This month's payment status per tenant — who has paid vs not — so
     * owner/manager can follow up on collection.
     */
    public function getMonthlyPaymentStatus(): array
    {
        $monthYear = now()->format('Y-m');

        $invoices = Invoice::where('month_year', $monthYear)
            ->with(['lease.tenant', 'lease.room'])
            ->get();

        $map = function ($invoice) {
            $tenant = $invoice->lease?->tenant;
            $phone = preg_replace('/[^0-9]/', '', (string) ($tenant->phone ?? ''));
            if ($phone !== '' && str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }

            return [
                'name' => $tenant?->display_name ?? $tenant?->name ?? '-',
                'room' => $invoice->lease?->room?->room_number ?? '-',
                'amount' => (float) $invoice->amount,
                'status' => $invoice->status,
                'due_date' => $invoice->due_date,
                'reference' => $invoice->reference_number,
                'wa' => $phone,
            ];
        };

        $paid = $invoices->where('status', 'paid')->map($map)->values();
        $unpaid = $invoices->whereIn('status', ['unpaid', 'pending'])
            ->sortBy('due_date')
            ->map($map)
            ->values();

        return [
            'month_label' => now()->translatedFormat('F Y'),
            'paid' => $paid,
            'unpaid' => $unpaid,
        ];
    }

    public function getUpcomingExpiringLeases()
    {
        return Lease::where('status', 'active')
            ->where('end_date', '<=', now()->addDays(30))
            ->with(['tenant', 'room'])
            ->orderBy('end_date')
            ->take(5)
            ->get();
    }

    public function render()
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $isOwner = $user->hasRole('owner');
        $isManager = $user->hasRole('manager');
        $isTenant = $user->hasRole('tenant');

        $data = [
            'isSuperAdmin' => $isSuperAdmin,
            'isOwner' => $isOwner,
            'isManager' => $isManager,
            'isTenant' => $isTenant,
        ];

        if ($isSuperAdmin) {
            $data['superadmin'] = $this->getSuperAdminData();
            $data['incomeChart'] = $this->getIncomeChartData();
        } elseif ($isOwner || $isManager) {
            $data['metrics'] = $this->getOwnerMetrics();
            $data['incomeChart'] = $this->getIncomeChartData();
            $data['recentInvoices'] = $this->getRecentInvoices();
            $data['recentMaintenance'] = $this->getRecentMaintenance();
            $data['expiringLeases'] = $this->getUpcomingExpiringLeases();
            $data['paymentStatus'] = $this->getMonthlyPaymentStatus();
        }

        if ($isTenant) {
            $data['tenant'] = $this->getTenantData();
        }

        return view('livewire.dashboard', $data);
    }
}

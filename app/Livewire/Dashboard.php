<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
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
        $isOwner = $user->hasRole('owner');
        $isManager = $user->hasRole('manager');
        $isTenant = $user->hasRole('tenant');

        $data = ['isOwner' => $isOwner, 'isManager' => $isManager, 'isTenant' => $isTenant];

        if ($isOwner || $isManager) {
            $data['metrics'] = $this->getOwnerMetrics();
            $data['incomeChart'] = $this->getIncomeChartData();
            $data['recentInvoices'] = $this->getRecentInvoices();
            $data['recentMaintenance'] = $this->getRecentMaintenance();
            $data['expiringLeases'] = $this->getUpcomingExpiringLeases();
        }

        if ($isTenant) {
            $data['tenant'] = $this->getTenantData();
        }

        return view('livewire.dashboard', $data);
    }
}

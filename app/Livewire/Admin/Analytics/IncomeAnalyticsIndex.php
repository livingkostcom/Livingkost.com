<?php

namespace App\Livewire\Admin\Analytics;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\OwnerWallet;
use App\Models\PaymentTransaction;
use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncomeAnalyticsIndex extends Component
{
    #[Url]
    public $startDate = '';

    #[Url]
    public $endDate = '';

    /**
     * Mount - check authorization
     */
    public function mount()
    {
        if (!Auth::check()) {
            redirect('/login');
        }

        if (!Auth::user()->can('view-income-report')) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini');
        }

        // Set default dates to current month if not provided
        if (!$this->startDate) {
            $this->startDate = now()->startOfMonth()->format('Y-m-d');
        }
        if (!$this->endDate) {
            $this->endDate = now()->format('Y-m-d');
        }
    }

    /**
     * Get summary statistics
     */
    public function getSummary()
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();

        $paidQuery = Invoice::where('status', 'paid')
            ->whereBetween('verified_at', [$startDate, $endDate]);

        $pendingQuery = Invoice::where('status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate]);

        $unpaidQuery = Invoice::where('status', 'unpaid')
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalQuery = Invoice::whereBetween('created_at', [$startDate, $endDate]);

        $received = (float) $paidQuery->sum('amount');

        // Expenses in the same period
        $expenses = (float) Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');

        // Platform fee on online (DOKU) payments settled in this period
        $ownerId = Auth::user()->ownerId();
        $feePercent = (float) (OwnerWallet::where('owner_id', $ownerId)->value('platform_fee_percent') ?? 0);
        $onlineGross = $ownerId
            ? (float) PaymentTransaction::where('owner_id', $ownerId)
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$startDate, $endDate])
                ->sum('amount')
            : 0;
        $platformFee = round($onlineGross * $feePercent / 100, 2);

        return [
            'received' => $received,
            'expenses' => $expenses,
            'platform_fee' => $platformFee,
            'net_income' => $received - $expenses - $platformFee,
            'pending_count' => $pendingQuery->count(),
            'pending_amount' => $pendingQuery->sum('amount'),
            'unpaid_count' => $unpaidQuery->count(),
            'unpaid_amount' => $unpaidQuery->sum('amount'),
            'total_invoices' => $totalQuery->count(),
            'total_amount' => $totalQuery->sum('amount'),
            'paid_count' => $paidQuery->count(),
        ];
    }

    /**
     * Get daily revenue data for chart
     */
    public function getRevenueData()
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();

        $data = Invoice::where('status', 'paid')
            ->whereBetween('verified_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(verified_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::createFromFormat('Y-m-d', $item->date)->translatedFormat('d M'),
                    'count' => $item->count,
                    'total' => (float) $item->total,
                ];
            });

        return $data;
    }

    /**
     * Get payment status breakdown
     */
    public function getStatusBreakdown()
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();

        $paid = Invoice::where('status', 'paid')
            ->whereBetween('verified_at', [$startDate, $endDate])
            ->count();

        $pending = Invoice::where('status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $unpaid = Invoice::where('status', 'unpaid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return [
            'paid' => $paid,
            'pending' => $pending,
            'unpaid' => $unpaid,
        ];
    }

    /**
     * Reset date filters
     */
    public function resetDates()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    /**
     * Set to last 7 days
     */
    public function setLast7Days()
    {
        $this->startDate = now()->subDays(7)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    /**
     * Set to last 30 days
     */
    public function setLast30Days()
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    /**
     * Set to this month
     */
    public function setThisMonth()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    /**
     * Render component
     */
    public function render()
    {
        $summary = $this->getSummary();
        $revenueData = $this->getRevenueData();
        $statusBreakdown = $this->getStatusBreakdown();

        $this->dispatch('charts-data-updated',
            revenueData: $revenueData,
            statusBreakdown: $statusBreakdown,
        );

        return view('livewire.admin.analytics.income-analytics-index', [
            'summary' => $summary,
            'revenueData' => $revenueData,
            'statusBreakdown' => $statusBreakdown,
        ]);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Lease;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'invoices:generate {--month= : Target month in YYYY-MM format (default: current month)}';

    protected $description = 'Generate monthly invoices for all active leases';

    public function handle(): int
    {
        $monthYear = $this->option('month') ?? now()->format('Y-m');

        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $monthYear)) {
            $this->error("Invalid month format. Use YYYY-MM (e.g. 2026-03)");
            return self::FAILURE;
        }

        $targetMonth = Carbon::createFromFormat('Y-m', $monthYear);

        $leases = Lease::where('status', 'active')
            ->where('start_date', '<=', $targetMonth->copy()->endOfMonth())
            ->where('end_date', '>=', $targetMonth->copy()->startOfMonth())
            ->with('room.roomType')
            ->get();

        if ($leases->isEmpty()) {
            $this->info("Tidak ada lease aktif untuk bulan {$monthYear}.");
            return self::SUCCESS;
        }

        $created = 0;
        $skipped = 0;

        foreach ($leases as $lease) {
            $exists = Invoice::where('lease_id', $lease->id)
                ->where('month_year', $monthYear)
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
                'month_year' => $monthYear,
                'status' => 'unpaid',
                'reference_number' => Invoice::generateInvoiceNumber(),
                'due_date' => $dueDate,
                'created_by' => null,
            ]);

            $created++;
        }

        $this->info("Selesai! {$created} invoice dibuat, {$skipped} dilewati (sudah ada).");

        return self::SUCCESS;
    }
}

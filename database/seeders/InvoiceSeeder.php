<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Lease;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = User::where('email', 'manager@fluty.com')->first();
        $leases = Lease::all();

        foreach ($leases as $lease) {
            // Create invoices for the last 3 months
            for ($i = 3; $i >= 0; $i--) {
                $invoiceDate = Carbon::now()->subMonths($i);
                $monthYear = $invoiceDate->format('Y-m');
                $dueDate = $invoiceDate->setDay($lease->due_date_per_month);

                // Determine status and payment proof
                $statuses = ['unpaid', 'unpaid', 'pending', 'paid']; // More unpaid invoices
                $status = $statuses[$i];
                $verifiedAt = null;
                $proofOfPayment = null;
                $verifiedBy = null;

                if ($status === 'paid') {
                    $verifiedAt = $invoiceDate->copy()->addDays(random_int(1, 5));
                    $proofOfPayment = 'payments/' . uniqid() . '.jpg';
                    $verifiedBy = $manager?->id ?? 1;
                } elseif ($status === 'pending') {
                    $proofOfPayment = 'payments/' . uniqid() . '.jpg';
                }

                Invoice::create([
                    'lease_id' => $lease->id,
                    'amount' => $lease->room->roomType->price,
                    'month_year' => $monthYear,
                    'status' => $status,
                    'reference_number' => 'INV-' . $lease->id . '-' . $monthYear,
                    'due_date' => $dueDate,
                    'proof_of_payment' => $proofOfPayment,
                    'verified_at' => $verifiedAt,
                    'created_by' => $manager?->id ?? 1,
                    'verified_by' => $verifiedBy,
                ]);
            }
        }
    }
}

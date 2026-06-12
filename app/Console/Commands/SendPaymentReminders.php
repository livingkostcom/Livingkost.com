<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Notifications\PaymentReminderNotification;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    protected $signature = 'invoices:send-reminders {--days-before=3 : Send reminder this many days before due date}';

    protected $description = 'Send payment reminder notifications for upcoming and overdue invoices';

    public function handle(): int
    {
        $daysBefore = (int) $this->option('days-before');
        $today = now()->startOfDay();

        $invoices = Invoice::whereIn('status', ['unpaid'])
            ->where('due_date', '<=', $today->copy()->addDays($daysBefore))
            ->with(['lease.tenant.user', 'lease.room.roomType'])
            ->get();

        if ($invoices->isEmpty()) {
            $this->info('Tidak ada invoice yang perlu dikirimi pengingat.');
            return self::SUCCESS;
        }

        $sent = 0;
        $skipped = 0;

        foreach ($invoices as $invoice) {
            $user = $invoice->lease->tenant->user;

            if (!$user) {
                $this->warn("Tenant '{$invoice->lease->tenant->display_name}' tidak terhubung akun, dilewati.");
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
            $this->line("  ✓ {$invoice->reference_number} → {$user->name} ({$reminderType})");
        }

        $this->info("Selesai! {$sent} notifikasi terkirim, {$skipped} dilewati.");

        return self::SUCCESS;
    }
}

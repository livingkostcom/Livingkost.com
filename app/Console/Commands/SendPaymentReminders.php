<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Notifications\PaymentReminderNotification;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    protected $signature = 'invoices:send-reminders {--days-before=1 : Send reminder this many days before due date}';

    protected $description = 'Send payment reminder notifications (in-app, email, WhatsApp) for upcoming and overdue invoices';

    public function handle(): int
    {
        $daysBefore = (int) $this->option('days-before');
        $today = now()->startOfDay();

        $invoices = Invoice::where('status', 'unpaid')
            ->where('due_date', '<=', $today->copy()->addDays($daysBefore))
            ->with(['lease.tenant.user', 'lease.tenant', 'lease.room.roomType'])
            ->get();

        if ($invoices->isEmpty()) {
            $this->info('Tidak ada invoice yang perlu dikirimi pengingat.');
            return self::SUCCESS;
        }

        $sent = 0;
        $skipped = 0;

        foreach ($invoices as $invoice) {
            $tenant = $invoice->lease->tenant;
            $user = $tenant->user;

            $reminderType = match (true) {
                $invoice->due_date->lt($today) => 'overdue',
                $invoice->due_date->eq($today) => 'due_today',
                default => 'upcoming',
            };

            // In-app + email notification
            if ($user) {
                $user->notify(new PaymentReminderNotification($invoice, $reminderType));
            }

            // WhatsApp
            $phone = $tenant->phone ?? null;
            if ($phone) {
                $msg = $this->buildWaMessage($invoice, $tenant->display_name ?? $tenant->name, $reminderType);
                WhatsAppService::send($phone, $msg);
            }

            if ($user || $phone) {
                $sent++;
                $channels = array_filter(['app' => $user ? 'in-app/email' : null, 'wa' => $phone ? 'WA' : null]);
                $this->line("  ✓ {$invoice->reference_number} → " . ($user->name ?? $tenant->name) . " [" . implode(', ', $channels) . "] ({$reminderType})");
            } else {
                $this->warn("  ✗ {$invoice->reference_number} → tidak ada akun/nomor WA, dilewati.");
                $skipped++;
            }
        }

        $this->info("Selesai! {$sent} dikirim, {$skipped} dilewati.");

        return self::SUCCESS;
    }

    private function buildWaMessage(Invoice $invoice, string $tenantName, string $reminderType): string
    {
        $amount = 'Rp ' . number_format($invoice->amount, 0, ',', '.');
        $due = $invoice->due_date->translatedFormat('d F Y');
        $ref = $invoice->reference_number;

        return match ($reminderType) {
            'overdue' => "Halo {$tenantName},\n\nTagihan kos Anda *{$ref}* sebesar *{$amount}* telah melewati jatuh tempo ({$due}).\n\nMohon segera lakukan pembayaran. Terima kasih.\n\n_Living Kost_",
            'due_today' => "Halo {$tenantName},\n\nTagihan kos Anda *{$ref}* sebesar *{$amount}* jatuh tempo *hari ini* ({$due}).\n\nSilakan segera lakukan pembayaran.\n\n_Living Kost_",
            default => "Halo {$tenantName},\n\nPengingat: tagihan kos Anda *{$ref}* sebesar *{$amount}* akan jatuh tempo pada *{$due}*.\n\nMohon lakukan pembayaran sebelum tanggal tersebut.\n\n_Living Kost_",
        };
    }
}

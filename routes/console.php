<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-generate invoice on H-1 (day before due date) at 07:00
Schedule::command('invoices:generate --h1')->dailyAt('07:00');

// Send payment reminders at H-1 (08:00) via in-app, email, and WhatsApp
Schedule::command('invoices:send-reminders --days-before=1')->dailyAt('08:00');

// Referral eligibility follows verified first-month payments; safe to run repeatedly.
Schedule::call(function () {
    \Illuminate\Support\Facades\DB::table('sales_agents')->distinct()->pluck('owner_id')->each(fn ($id) => \App\Services\ReferralRewards::syncOwner((int) $id));
})->name('referrals:reconcile')->everyFiveMinutes()->withoutOverlapping();

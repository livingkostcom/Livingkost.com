<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DokuWebhookController;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Auth;

// Public Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// DOKU payment notification (webhook) — public, no auth/CSRF
Route::post('/doku/notification', [DokuWebhookController::class, 'handle'])->name('doku.notification');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Property Management Routes
    Route::get('/properties', function () {
        return view('properties.index');
    })->middleware('can:viewAny,App\Models\Property')->name('properties.index');

    // Room Type Management Routes
    Route::get('/room-types', function () {
        return view('room-types.index');
    })->middleware('can:viewAny,App\Models\RoomType')->name('room-types.index');

    // Room Management Routes
    Route::get('/rooms', function () {
        return view('rooms.index');
    })->middleware('can:viewAny,App\Models\Room')->name('rooms.index');

    // Tenant Management Routes
    Route::get('/tenants', function () {
        return view('tenants.index');
    })->middleware('can:viewAny,App\Models\Tenant')->name('tenants.index');

    // Lease Management Routes
    Route::get('/leases', function () {
        return view('leases.index');
    })->middleware('can:viewAny,App\Models\Lease')->name('leases.index');

    // Invoice Management Routes (Admin/Manager)
    Route::get('/invoices', function () {
        return view('invoices.index');
    })->middleware('can:viewAny,App\Models\Invoice')->name('invoices.index');
    
    Route::get('/invoices/create', function () {
        return view('invoices.create');
    })->middleware('can:create,App\Models\Invoice')->name('invoices.create');
    
    Route::get('/invoices/{invoice}/edit', function (\App\Models\Invoice $invoice) {
        return view('invoices.edit', ['invoice' => $invoice]);
    })->middleware('can:update,invoice')->name('invoices.edit');

    // Payment Verification Routes (Admin/Manager)
    Route::get('/payment-verifications', function () {
        return view('admin.payment.verification');
    })->middleware(['auth'])->name('payment-verifications.index');

    // Receipt Download Routes
    Route::get('/receipts/{receipt}/download', function (\App\Models\Receipt $receipt) {
        if (!Auth::user()->can('verify-payment') && Auth::user()->id !== $receipt->invoice->lease->tenant->user_id) {
            abort(403, 'Unauthorized');
        }
        
        return response()->download(
            storage_path('app/' . $receipt->pdf_path),
            $receipt->receipt_number . '.pdf'
        );
    })->middleware(['auth'])->name('receipts.download');

    // Payment proof — private file, streamed inline to authorized users only
    Route::get('/payment-proofs/{invoice}/view', function (\App\Models\Invoice $invoice) {
        if (!Auth::user()->can('verify-payment') && Auth::user()->id !== $invoice->lease->tenant->user_id) {
            abort(403, 'Unauthorized');
        }

        if (!$invoice->proof_of_payment) {
            abort(404);
        }

        $path = storage_path('app/private/' . $invoice->proof_of_payment);
        if (!is_file($path)) {
            abort(404);
        }

        return response()->file($path);
    })->middleware(['auth'])->name('payment-proofs.view');

    // Income Analytics Routes
    Route::get('/analytics/income', function () {
        return view('admin.analytics.income');
    })->middleware(['auth'])->name('analytics.income');

    // Tenant Invoice Routes (Self-service)
    Route::get('/tenant/invoices', function () {
        return view('tenant.invoices.index');
    })->middleware('can:viewAny,App\Models\Invoice')->name('tenant.invoices.index');

    Route::get('/reports', function () {
        return view('reports.index');
    })->middleware(['auth'])->name('reports.index');

    // Maintenance Request Routes
    Route::get('/maintenance', function () {
        if (Auth::user()->hasRole('tenant')) {
            return view('tenant.maintenance.index');
        }
        return view('admin.maintenance.index');
    })->middleware(['auth'])->name('maintenance.index');

    // Expense Routes
    Route::get('/expenses', function () {
        return view('admin.expenses.index');
    })->middleware(['auth'])->name('expenses.index');

    // Announcement Routes
    Route::get('/announcements', function () {
        if (Auth::user()->hasRole('tenant')) {
            return view('tenant.announcements.index');
        }
        return view('admin.announcements.index');
    })->middleware(['auth'])->name('announcements.index');

    // User Management Routes (Owner & Super Admin)
    Route::get('/users', function () {
        return view('admin.users.index');
    })->middleware('can:manage-users')->name('users.index');

    // Settings Routes
    Route::get('/settings', function () {
        return view('admin.settings.index');
    })->middleware(['auth'])->name('settings.index');

    // Payment partnerships (super-admin only)
    Route::get('/payment-partners', function () {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);
        return view('admin.payment-partners.index');
    })->middleware(['auth'])->name('payment-partners.index');
});

// Welcome page (public) - redirect to login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Setting;
use App\Observers\InvoiceObserver;
use App\Observers\LeaseObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers
        Invoice::observe(InvoiceObserver::class);
        Lease::observe(LeaseObserver::class);

        // Super-admins bypass every policy/gate check (full platform control).
        Gate::before(function ($user) {
            return $user->isSuperAdmin() ? true : null;
        });

        // Use Tailwind pagination view
        Paginator::useTailwind();

        // Share app settings with all views
        View::composer('*', function ($view) {
            if (Schema::hasTable('settings')) {
                $view->with('appName', Setting::getValue('app_name'));
                $view->with('appTagline', Setting::getValue('app_tagline'));
            } else {
                $view->with('appName', 'Living Kost');
                $view->with('appTagline', 'Sistem Manajemen Kos');
            }
        });
    }
}

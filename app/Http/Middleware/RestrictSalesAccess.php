<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictSalesAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Sales uses dedicated pages, never owner Livewire endpoints or raw records.
        if ($request->user()?->hasRole('sales')) {
            abort_unless($request->routeIs('referrals.sales', 'referrals.logout', 'dashboard', 'login'), 403);
        }
        return $next($request);
    }
}

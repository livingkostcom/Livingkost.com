<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Sidebar extends Component
{
    public bool $isOpen = false;

    public function toggleSidebar()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function closeSidebar()
    {
        $this->isOpen = false;
    }

    public function getMenuItems()
    {
        $user = Auth::user();
        $items = [
            [
                'label' => 'Dashboard',
                'icon' => 'M3 12l2-12v12H3zm18 0l-2-12v12h2zm-9-9h.01M12 3a9 9 0 110 18 9 9 0 010-18z',
                'route' => 'dashboard',
                'active' => request()->route()->getName() === 'dashboard',
            ],
        ];

        // Owner menu items
        if ($user->hasPermissionTo('view-properties')) {
            $items[] = [
                'label' => 'Properti',
                'icon' => 'M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z',
                'route' => 'properties.index',
                'active' => request()->route()->getName() === 'properties.index',
            ];
        }

        if ($user->hasPermissionTo('view-room-types')) {
            $items[] = [
                'label' => 'Tipe Ruangan',
                'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
                'route' => 'room-types.index',
                'active' => request()->route()->getName() === 'room-types.index',
            ];
        }

        if ($user->hasPermissionTo('view-rooms')) {
            $items[] = [
                'label' => 'Ruangan',
                'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2m0 0V7a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m0 0H7a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2z',
                'route' => 'rooms.index',
                'active' => request()->route()->getName() === 'rooms.index',
            ];
        }

        if ($user->hasPermissionTo('view-tenants')) {
            $items[] = [
                'label' => 'Penyewa',
                'icon' => 'M12 4.354a4 4 0 110 8.646 4 4 0 010-8.646M9 9H7.5A1.5 1.5 0 006 10.5V12a6 6 0 0012 0v-1.5a1.5 1.5 0 00-1.5-1.5H15m-6 4.5h6a1 1 0 01.894.553l.894 1.789a1 1 0 01-.894 1.447H8.106a1 1 0 01-.894-1.447l.894-1.789A1 1 0 019 13.5z',
                'route' => 'tenants.index',
                'active' => request()->route()->getName() === 'tenants.index',
            ];
        }

        if ($user->hasPermissionTo('view-leases')) {
            $items[] = [
                'label' => 'Kontrak Sewa',
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'route' => 'leases.index',
                'active' => request()->route()->getName() === 'leases.index',
            ];
        }

        if ($user->hasPermissionTo('view-invoices')) {
            // Tenant goes to tenant invoices, others go to admin invoices
            $invoiceRoute = $user->hasRole('tenant') ? 'tenant.invoices.index' : 'invoices.index';
            $items[] = [
                'label' => 'Invoice',
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'route' => $invoiceRoute,
                'active' => request()->route()->getName() === $invoiceRoute,
            ];
        }

        if ($user->hasPermissionTo('verify-payment')) {
            $items[] = [
                'label' => 'Verifikasi Pembayaran',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'route' => 'payment-verifications.index',
                'active' => request()->route()->getName() === 'payment-verifications.index',
            ];
        }

        if ($user->hasPermissionTo('view-income-report')) {
            $items[] = [
                'label' => 'Analisis Pendapatan',
                'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                'route' => 'analytics.income',
                'active' => request()->route()->getName() === 'analytics.income',
            ];
        }

        if ($user->hasPermissionTo('view-maintenance')) {
            $items[] = [
                'label' => 'Perbaikan',
                'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                'route' => 'maintenance.index',
                'active' => request()->route()->getName() === 'maintenance.index',
            ];
        }

        if ($user->hasPermissionTo('view-reports')) {
            $items[] = [
                'label' => 'Laporan',
                'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                'route' => 'reports.index',
                'active' => request()->route()->getName() === 'reports.index',
            ];
        }

        if ($user->hasPermissionTo('view-expenses')) {
            $items[] = [
                'label' => 'Pengeluaran',
                'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                'route' => 'expenses.index',
                'active' => request()->route()->getName() === 'expenses.index',
            ];
        }

        if ($user->hasPermissionTo('view-announcements')) {
            $items[] = [
                'label' => 'Pengumuman',
                'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
                'route' => 'announcements.index',
                'active' => request()->route()->getName() === 'announcements.index',
            ];
        }

        if ($user->hasPermissionTo('manage-users')) {
            $items[] = [
                'label' => 'Kelola User',
                'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                'route' => 'users.index',
                'active' => request()->route()->getName() === 'users.index',
            ];
        }

        if ($user->isOwner()) {
            $items[] = [
                'label' => 'Dompet',
                'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                'route' => 'wallet.index',
                'active' => request()->route()->getName() === 'wallet.index',
            ];
        }

        if ($user->isSuperAdmin()) {
            $items[] = [
                'label' => 'Kemitraan Pembayaran',
                'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                'route' => 'payment-partners.index',
                'active' => request()->route()->getName() === 'payment-partners.index',
            ];
            $items[] = [
                'label' => 'Pencairan',
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'route' => 'disbursements.index',
                'active' => request()->route()->getName() === 'disbursements.index',
            ];
            $items[] = [
                'label' => 'Pendapatan Lain',
                'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                'route' => 'company-income.index',
                'active' => request()->route()->getName() === 'company-income.index',
            ];
            $items[] = [
                'label' => 'Pengeluaran Perusahaan',
                'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                'route' => 'company-expenses.index',
                'active' => request()->route()->getName() === 'company-expenses.index',
            ];
        }

        if ($user->hasPermissionTo('manage-settings')) {
            $items[] = [
                'label' => 'Pengaturan',
                'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                'route' => 'settings.index',
                'active' => request()->route()->getName() === 'settings.index',
            ];
        }

        return $items;
    }

    public function render()
    {
        $user = Auth::user();
        $menuItems = $this->getMenuItems();
        
        // Debug logging
        \Log::info('Sidebar render', [
            'user_id' => $user?->id,
            'user_roles' => $user?->getRoleNames(),
            'user_permissions' => $user?->getPermissionNames(),
            'menu_items_count' => count($menuItems),
            'has_view_invoices' => $user?->hasPermissionTo('view-invoices'),
        ]);

        return view('livewire.sidebar', [
            'menuItems' => $menuItems,
        ]);
    }
}

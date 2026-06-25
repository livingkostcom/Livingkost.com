<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SettingIndex extends Component
{
    // General
    public string $app_name = '';
    public string $app_tagline = '';
    public string $app_address = '';
    public string $app_phone = '';
    public string $app_email = '';

    // Payment
    public string $bank_name = '';
    public string $bank_account_number = '';
    public string $bank_account_holder = '';
    public string $payment_instructions = '';

    // Late Fee
    public string $late_fee_enabled = '0';
    public string $late_fee_type = 'fixed';
    public string $late_fee_amount = '0';
    public string $late_fee_grace_days = '3';

    public string $activeTab = 'general';

    public function mount()
    {
        if (!Auth::user()->can('manage-settings')) {
            abort(403);
        }

        $this->loadSettings();
    }

    private function loadSettings()
    {
        $defaults = Setting::defaults();

        foreach ($defaults as $key => $default) {
            if (property_exists($this, $key)) {
                $this->$key = Setting::get($key, $default) ?? $default;
            }
        }
    }

    public function saveGeneral()
    {
        $this->validate([
            'app_name' => 'required|string|max:255',
            'app_tagline' => 'nullable|string|max:255',
            'app_address' => 'nullable|string|max:500',
            'app_phone' => 'nullable|string|max:20',
            'app_email' => 'nullable|email|max:255',
        ]);

        // app_name/app_tagline are owner-scoped (Setting::GLOBAL_KEYS): an owner
        // saves their own kos name/tagline; a super-admin writes the global
        // default that owners fall back to.
        $keys = ['app_name', 'app_tagline', 'app_address', 'app_phone', 'app_email'];
        foreach ($keys as $key) {
            Setting::set($key, $this->$key, 'general');
        }

        session()->flash('message', 'Pengaturan umum berhasil disimpan.');
    }

    public function savePayment()
    {
        $this->validate([
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:255',
            'payment_instructions' => 'nullable|string',
        ]);

        $keys = ['bank_name', 'bank_account_number', 'bank_account_holder', 'payment_instructions'];
        foreach ($keys as $key) {
            Setting::set($key, $this->$key, 'payment');
        }

        session()->flash('message', 'Pengaturan pembayaran berhasil disimpan.');
    }

    public function saveLateFee()
    {
        $this->validate([
            'late_fee_enabled' => 'required|in:0,1',
            'late_fee_type' => 'required|in:fixed,percentage',
            'late_fee_amount' => 'required|numeric|min:0',
            'late_fee_grace_days' => 'required|integer|min:0|max:30',
        ]);

        $keys = ['late_fee_enabled', 'late_fee_type', 'late_fee_amount', 'late_fee_grace_days'];
        foreach ($keys as $key) {
            Setting::set($key, $this->$key, 'late_fee');
        }

        session()->flash('message', 'Pengaturan denda keterlambatan berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.admin.setting-index');
    }
}

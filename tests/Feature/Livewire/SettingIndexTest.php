<?php

use App\Livewire\Admin\SettingIndex;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    $this->owner = User::factory()->create();
    $this->owner->assignRole('owner');
});

test('setting index component renders for owner', function () {
    Livewire::actingAs($this->owner)
        ->test(SettingIndex::class)
        ->assertOk();
});

test('setting index can save general settings', function () {
    Livewire::actingAs($this->owner)
        ->test(SettingIndex::class)
        ->set('app_name', 'My Custom Kos')
        ->set('app_tagline', 'Best Kos Ever')
        ->call('saveGeneral')
        ->assertHasNoErrors();

    expect(Setting::get('app_name'))->toBe('My Custom Kos')
        ->and(Setting::get('app_tagline'))->toBe('Best Kos Ever');
});

test('setting index can save payment settings', function () {
    Livewire::actingAs($this->owner)
        ->test(SettingIndex::class)
        ->set('bank_name', 'BCA')
        ->set('bank_account_number', '1234567890')
        ->set('bank_account_holder', 'John Doe')
        ->call('savePayment')
        ->assertHasNoErrors();

    expect(Setting::get('bank_name'))->toBe('BCA');
});

test('setting index can save late fee settings', function () {
    Livewire::actingAs($this->owner)
        ->test(SettingIndex::class)
        ->set('late_fee_enabled', true)
        ->set('late_fee_type', 'fixed')
        ->set('late_fee_amount', '50000')
        ->set('late_fee_grace_days', '5')
        ->call('saveLateFee')
        ->assertHasNoErrors();

    expect(Setting::get('late_fee_enabled'))->toBe('1');
});

test('setting index denies access to manager', function () {
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    Livewire::actingAs($manager)
        ->test(SettingIndex::class)
        ->assertForbidden();
});

test('setting index denies access to tenant', function () {
    $tenant = User::factory()->create();
    $tenant->assignRole('tenant');

    Livewire::actingAs($tenant)
        ->test(SettingIndex::class)
        ->assertForbidden();
});

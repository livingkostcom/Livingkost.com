<?php

use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;

test('lease belongs to tenant', function () {
    $lease = Lease::factory()->create();

    expect($lease->tenant)->toBeInstanceOf(Tenant::class);
});

test('lease belongs to room', function () {
    $lease = Lease::factory()->create();

    expect($lease->room)->toBeInstanceOf(Room::class);
});

test('lease has many invoices', function () {
    $lease = Lease::factory()->create();
    Invoice::factory()->create(['lease_id' => $lease->id, 'month_year' => '2025-01']);
    Invoice::factory()->create(['lease_id' => $lease->id, 'month_year' => '2025-02']);
    Invoice::factory()->create(['lease_id' => $lease->id, 'month_year' => '2025-03']);

    expect($lease->invoices)->toHaveCount(3);
});

test('lease casts dates properly', function () {
    $lease = Lease::factory()->create([
        'start_date' => '2025-01-01',
        'end_date' => '2025-12-31',
    ]);

    expect($lease->start_date)->toBeInstanceOf(Carbon\Carbon::class)
        ->and($lease->end_date)->toBeInstanceOf(Carbon\Carbon::class)
        ->and($lease->start_date->format('Y-m-d'))->toBe('2025-01-01')
        ->and($lease->end_date->format('Y-m-d'))->toBe('2025-12-31');
});

test('lease casts deposit amount as decimal', function () {
    $lease = Lease::factory()->create(['deposit_amount' => 1500000]);

    expect($lease->deposit_amount)->toBe('1500000.00');
});

test('lease uses soft deletes', function () {
    $lease = Lease::factory()->create();
    $leaseId = $lease->id;
    $lease->delete();

    expect(Lease::find($leaseId))->toBeNull()
        ->and(Lease::withTrashed()->find($leaseId))->not->toBeNull();
});

test('lease factory states work correctly', function () {
    expect(Lease::factory()->pending()->create()->status)->toBe('pending')
        ->and(Lease::factory()->completed()->create()->status)->toBe('completed')
        ->and(Lease::factory()->terminated()->create()->status)->toBe('terminated');
});

test('lease has creator relationship', function () {
    $user = User::factory()->create();
    $lease = Lease::factory()->create(['created_by' => $user->id]);

    expect($lease->creator)->toBeInstanceOf(User::class)
        ->and($lease->creator->id)->toBe($user->id);
});

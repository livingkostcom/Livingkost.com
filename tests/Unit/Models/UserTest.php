<?php

use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\User;

test('user can be created with factory', function () {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->not->toBeEmpty()
        ->and($user->email)->not->toBeEmpty();
});

test('user has one tenant', function () {
    $user = User::factory()->create();
    Tenant::factory()->create(['user_id' => $user->id]);

    expect($user->tenant)->toBeInstanceOf(Tenant::class);
});

test('user has many created invoices', function () {
    $user = User::factory()->create();
    Invoice::factory()->count(3)->create(['created_by' => $user->id]);

    expect($user->createdInvoices)->toHaveCount(3);
});

test('user has many verified invoices', function () {
    $user = User::factory()->create();
    Invoice::factory()->count(2)->paid()->create(['verified_by' => $user->id]);

    expect($user->verifiedInvoices)->toHaveCount(2);
});

test('user has many created leases', function () {
    $user = User::factory()->create();
    Lease::factory()->count(2)->create(['created_by' => $user->id]);

    expect($user->createdLeases)->toHaveCount(2);
});

test('user password is hashed', function () {
    $user = User::factory()->create(['password' => 'testpassword']);

    expect($user->password)->not->toBe('testpassword');
});

test('user unverified factory state works', function () {
    $user = User::factory()->unverified()->create();

    expect($user->email_verified_at)->toBeNull();
});

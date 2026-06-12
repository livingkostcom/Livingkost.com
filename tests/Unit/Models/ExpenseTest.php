<?php

use App\Models\Expense;
use App\Models\Property;
use App\Models\User;

test('expense can be created with factory', function () {
    $expense = Expense::factory()->create();

    expect($expense)->toBeInstanceOf(Expense::class)
        ->and($expense->title)->not->toBeEmpty();
});

test('expense belongs to property', function () {
    $expense = Expense::factory()->create();

    expect($expense->property)->toBeInstanceOf(Property::class);
});

test('expense belongs to creator', function () {
    $expense = Expense::factory()->create();

    expect($expense->creator)->toBeInstanceOf(User::class);
});

test('expense casts amount as decimal', function () {
    $expense = Expense::factory()->create(['amount' => 500000]);

    expect($expense->amount)->toBe('500000.00');
});

test('expense casts expense_date as date', function () {
    $expense = Expense::factory()->create(['expense_date' => '2025-03-15']);

    expect($expense->expense_date)->toBeInstanceOf(Carbon\Carbon::class)
        ->and($expense->expense_date->format('Y-m-d'))->toBe('2025-03-15');
});

test('expense uses soft deletes', function () {
    $expense = Expense::factory()->create();
    $expense->delete();

    expect(Expense::count())->toBe(0)
        ->and(Expense::withTrashed()->count())->toBe(1);
});

test('expense get category label returns correct labels', function () {
    expect(Expense::getCategoryLabel('maintenance'))->toBe('Perbaikan')
        ->and(Expense::getCategoryLabel('utility'))->toBe('Utilitas (Listrik/Air)')
        ->and(Expense::getCategoryLabel('cleaning'))->toBe('Kebersihan')
        ->and(Expense::getCategoryLabel('supplies'))->toBe('Perlengkapan')
        ->and(Expense::getCategoryLabel('salary'))->toBe('Gaji')
        ->and(Expense::getCategoryLabel('tax'))->toBe('Pajak')
        ->and(Expense::getCategoryLabel('insurance'))->toBe('Asuransi')
        ->and(Expense::getCategoryLabel('other'))->toBe('Lainnya');
});

test('expense get category label returns raw value for unknown category', function () {
    expect(Expense::getCategoryLabel('unknown'))->toBe('unknown');
});

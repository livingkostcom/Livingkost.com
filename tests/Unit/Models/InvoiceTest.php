<?php

use App\Models\Invoice;
use App\Models\Lease;
use App\Models\User;

test('invoice belongs to lease', function () {
    $invoice = Invoice::factory()->create();

    expect($invoice->lease)->toBeInstanceOf(Lease::class);
});

test('invoice generates unique invoice numbers', function () {
    $number1 = Invoice::generateInvoiceNumber();
    Invoice::factory()->create(['reference_number' => $number1]);

    $number2 = Invoice::generateInvoiceNumber();

    expect($number1)->toStartWith('INV-')
        ->and($number2)->toStartWith('INV-')
        ->and($number1)->not->toBe($number2);
});

test('invoice number format is correct', function () {
    $number = Invoice::generateInvoiceNumber();

    expect($number)->toMatch('/^INV-\d{8}-\d{4}$/');
});

test('invoice number sequence increments', function () {
    $number1 = Invoice::generateInvoiceNumber();
    Invoice::factory()->create(['reference_number' => $number1]);

    $number2 = Invoice::generateInvoiceNumber();

    $seq1 = (int) substr($number1, -4);
    $seq2 = (int) substr($number2, -4);

    expect($seq2)->toBe($seq1 + 1);
});

test('invoice casts amount as decimal', function () {
    $invoice = Invoice::factory()->create(['amount' => 1500000]);

    expect($invoice->amount)->toBe('1500000.00');
});

test('invoice casts due_date as date', function () {
    $invoice = Invoice::factory()->create(['due_date' => '2025-06-15']);

    expect($invoice->due_date)->toBeInstanceOf(Carbon\Carbon::class)
        ->and($invoice->due_date->format('Y-m-d'))->toBe('2025-06-15');
});

test('invoice casts verified_at as datetime', function () {
    $invoice = Invoice::factory()->paid()->create();

    expect($invoice->verified_at)->toBeInstanceOf(Carbon\Carbon::class);
});

test('invoice uses soft deletes', function () {
    $invoice = Invoice::factory()->create();
    $invoice->delete();

    expect(Invoice::count())->toBe(0)
        ->and(Invoice::withTrashed()->count())->toBe(1);
});

test('invoice factory states work correctly', function () {
    $paid = Invoice::factory()->paid()->create();
    $pending = Invoice::factory()->pending()->create();

    expect($paid->status)->toBe('paid')
        ->and($paid->verified_at)->not->toBeNull()
        ->and($pending->status)->toBe('pending')
        ->and($pending->proof_of_payment)->not->toBeNull();
});

test('invoice has creator and verifier relationships', function () {
    $creator = User::factory()->create();
    $verifier = User::factory()->create();

    $invoice = Invoice::factory()->create([
        'created_by' => $creator->id,
        'verified_by' => $verifier->id,
    ]);

    expect($invoice->creator->id)->toBe($creator->id)
        ->and($invoice->verifier->id)->toBe($verifier->id);
});

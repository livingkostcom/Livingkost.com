<?php

use App\Livewire\Admin\ExpenseIndex;
use App\Models\Expense;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    $this->owner = User::factory()->create();
    $this->owner->assignRole('owner');
});

test('expense index component renders', function () {
    Livewire::actingAs($this->owner)
        ->test(ExpenseIndex::class)
        ->assertOk();
});

test('expense index shows expenses', function () {
    $expense = Expense::factory()->create([
        'created_by' => $this->owner->id,
        'expense_date' => now(),
    ]);

    Livewire::actingAs($this->owner)
        ->test(ExpenseIndex::class)
        ->assertSee($expense->title);
});

test('expense index can create expense', function () {
    $property = Property::factory()->create();

    Livewire::actingAs($this->owner)
        ->test(ExpenseIndex::class)
        ->call('openCreateModal')
        ->assertSet('showModal', true)
        ->set('title', 'Test Expense')
        ->set('description', 'Test description')
        ->set('amount', 500000)
        ->set('expense_date', now()->format('Y-m-d'))
        ->set('category', 'maintenance')
        ->set('property_id', $property->id)
        ->call('save')
        ->assertHasNoErrors();

    expect(Expense::where('title', 'Test Expense')->exists())->toBeTrue();
});

test('expense index validates required fields', function () {
    Livewire::actingAs($this->owner)
        ->test(ExpenseIndex::class)
        ->call('openCreateModal')
        ->set('title', '')
        ->set('amount', '')
        ->set('expense_date', '')
        ->set('category', '')
        ->call('save')
        ->assertHasErrors(['title', 'amount', 'expense_date', 'category']);
});

test('expense index can delete expense', function () {
    $expense = Expense::factory()->create(['created_by' => $this->owner->id]);

    Livewire::actingAs($this->owner)
        ->test(ExpenseIndex::class)
        ->call('confirmDelete', $expense->id)
        ->call('delete');

    expect(Expense::find($expense->id))->toBeNull();
});

test('expense index can search', function () {
    Expense::factory()->create([
        'title' => 'Perbaikan AC',
        'created_by' => $this->owner->id,
        'expense_date' => now(),
    ]);
    Expense::factory()->create([
        'title' => 'Gaji Pegawai',
        'created_by' => $this->owner->id,
        'expense_date' => now(),
    ]);

    Livewire::actingAs($this->owner)
        ->test(ExpenseIndex::class)
        ->set('search', 'Perbaikan')
        ->assertSee('Perbaikan AC')
        ->assertDontSee('Gaji Pegawai');
});

test('expense index can filter by category', function () {
    Expense::factory()->create([
        'category' => 'maintenance',
        'created_by' => $this->owner->id,
        'expense_date' => now(),
    ]);
    Expense::factory()->create([
        'category' => 'salary',
        'created_by' => $this->owner->id,
        'expense_date' => now(),
    ]);

    Livewire::actingAs($this->owner)
        ->test(ExpenseIndex::class)
        ->set('categoryFilter', 'maintenance')
        ->assertSee('Perbaikan');
});

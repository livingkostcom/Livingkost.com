<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Lease;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;
        $date = now()->subMonths($sequence);

        return [
            'lease_id' => Lease::factory(),
            'amount' => fake()->randomFloat(2, 500000, 3000000),
            'month_year' => $date->format('Y-m'),
            'status' => 'unpaid',
            'reference_number' => 'INV-' . date('Ymd') . '-' . fake()->unique()->numerify('####'),
            'due_date' => now()->addDays(fake()->numberBetween(7, 30)),
        ];
    }

    public function paid(): static
    {
        return $this->state([
            'status' => 'paid',
            'verified_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state([
            'status' => 'pending',
            'proof_of_payment' => 'proofs/test-proof.jpg',
        ]);
    }
}

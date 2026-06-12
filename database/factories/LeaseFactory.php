<?php

namespace Database\Factories;

use App\Models\Lease;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaseFactory extends Factory
{
    protected $model = Lease::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'tenant_id' => Tenant::factory(),
            'room_id' => Room::factory(),
            'start_date' => $startDate,
            'end_date' => now()->addYear(),
            'due_date_per_month' => fake()->numberBetween(1, 28),
            'deposit_amount' => fake()->randomFloat(2, 500000, 2000000),
            'status' => 'active',
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed']);
    }

    public function terminated(): static
    {
        return $this->state(['status' => 'terminated']);
    }
}

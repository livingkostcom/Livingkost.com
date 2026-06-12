<?php

namespace Database\Factories;

use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceRequestFactory extends Factory
{
    protected $model = MaintenanceRequest::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'room_id' => Room::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['electrical', 'plumbing', 'furniture', 'cleaning', 'other']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'status' => 'pending',
        ];
    }

    public function inProgress(): static
    {
        return $this->state(['status' => 'in_progress']);
    }

    public function completed(): static
    {
        return $this->state([
            'status' => 'completed',
            'resolved_at' => now(),
        ]);
    }
}

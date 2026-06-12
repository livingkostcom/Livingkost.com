<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'room_type_id' => RoomType::factory(),
            'room_number' => fake()->unique()->numerify('###'),
            'floor' => fake()->numberBetween(1, 3),
            'status' => 'available',
        ];
    }

    public function occupied(): static
    {
        return $this->state(['status' => 'occupied']);
    }

    public function maintenance(): static
    {
        return $this->state(['status' => 'maintenance']);
    }
}

<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomTypeFactory extends Factory
{
    protected $model = RoomType::class;

    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'name' => fake()->randomElement(['Standard', 'Deluxe', 'Suite', 'Premium']),
            'price' => fake()->randomFloat(2, 500000, 3000000),
            'facilities' => ['AC', 'WiFi', 'Kamar Mandi Dalam'],
        ];
    }
}

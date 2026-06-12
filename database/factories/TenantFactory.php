<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'nik' => fake()->unique()->numerify('################'),
            'phone' => fake()->phoneNumber(),
            'emergency_contact' => fake()->phoneNumber(),
            'status' => 'active',
            'user_id' => null,
        ];
    }

    public function withUser(?User $user = null): static
    {
        return $this->state(fn () => [
            'user_id' => $user?->id ?? User::factory(),
        ]);
    }
}

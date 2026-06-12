<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'content' => fake()->paragraphs(2, true),
            'priority' => fake()->randomElement(['normal', 'important', 'urgent']),
            'target' => 'all',
            'published_at' => now(),
            'expires_at' => now()->addDays(30),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }

    public function expired(): static
    {
        return $this->state([
            'published_at' => now()->subDays(60),
            'expires_at' => now()->subDays(1),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function urgent(): static
    {
        return $this->state(['priority' => 'urgent']);
    }
}

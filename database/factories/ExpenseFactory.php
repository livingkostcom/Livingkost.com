<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'amount' => fake()->randomFloat(2, 50000, 5000000),
            'expense_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'category' => fake()->randomElement(['maintenance', 'utility', 'cleaning', 'supplies', 'salary', 'tax', 'insurance', 'other']),
            'created_by' => User::factory(),
        ];
    }
}

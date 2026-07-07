<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\BankAccount;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bank_account_id' => fn (array $attributes) => BankAccount::factory()->state(['user_id' => $attributes['user_id']]),
            'description' => fake()->sentence(3),
            'category_id' => fn (array $attributes) => Category::factory()->state(['user_id' => $attributes['user_id']]),
            'budget_id' => fn (array $attributes) => Budget::factory()->state(['user_id' => $attributes['user_id']]),
            'note' => fake()->sentence(5),
            'amount' => fake()->numberBetween(9999, 9999),
            'date' => fake()->dateTimeBetween('-1 year', 'now'),
            'type' => fake()->randomElement(TransactionType::class),
        ];
    }
}

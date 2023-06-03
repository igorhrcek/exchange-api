<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
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
        $user = User::factory()->create();

        return [
            'from_account_id' => Account::factory()->create(['user_id' => $user->id]),
            'to_account_id' => Account::factory()->create(['user_id' => $user->id]),
            'amount' => fake()->numberBetween(1, 15000),
            'reference' => fake()->unique()->regexify('[A-Z0-9]{20}')
        ];
    }
}

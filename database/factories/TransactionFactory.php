<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

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
            'account_id' => Account::factory()->create(['user_id' => $user->id]),
            'amount' => fake()->randomFloat(2, 1, 15000),
            'reference' => fake()->unique()->regexify('[A-Z0-9]{20}')
        ];
    }
}

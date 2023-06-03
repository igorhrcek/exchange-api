<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Str;
use App\Models\Currency;
use App\Models\User;
use App\Models\Account;
use Illuminate\Database\QueryException;

class AccountTest extends TestCase
{
    public function test_given_user_id_and_currency_id_when_creating_account_it_will_create_account(): void {
        $currency = Currency::factory()->create();
        $user = User::factory()->create();

        $account = Account::create([
            'user_id' => $user->id,
            'currency_id' => $currency->id,
            'uuid' => Str::uuid(),
            'balance' => 1000
        ]);

        $this->assertDatabaseCount('accounts', 1);
        $this->assertEquals($account->currency_id, $currency->id);
        $this->assertEquals($account->user_id, $user->id);
        $this->assertEquals($account->balance, 1000);
    }

    public function test_given_user_id_and_same_currency_id_multiple_times_when_creating_account_it_will_not_create_account(): void {
        $account = Account::factory()->create();

        $this->expectException(QueryException::class);
        Account::create([
            'user_id' => $account->user_id,
            'currency_id' => $account->currency_id,
            'uuid' => Str::uuid(),
            'balance' => 3000
        ]);
    }

    public function test_not_given_user_id_when_creating_account_it_will_cause_database_exception(): void {
        $this->expectException(QueryException::class);

        Account::create([
            'currency_id' => Currency::factory()->create()->id,
            'uuid' => Str::uuid(),
            'balance' => 1000
        ]);
    }

    public function test_not_given_currency_id_when_creating_account_it_will_cause_database_exception(): void {
        $this->expectException(QueryException::class);

        Account::create([
            'user_id' => User::factory()->create()->id,
            'uuid' => Str::uuid(),
            'balance' => 1000
        ]);
    }

    public function test_not_given_uuid_when_creating_account_it_will_cause_database_exception(): void {
        $this->expectException(QueryException::class);

        Account::create([
            'user_id' => User::factory()->create()->id,
            'currency_id' => Currency::factory()->create()->id,
            'balance' => 1000
        ]);
    }

    public function test_not_given_staring_balance_when_creating_account_it_will_cause_database_exception(): void {
        $this->expectException(QueryException::class);

        Account::create([
            'user_id' => User::factory()->create()->id,
            'currency_id' => Currency::factory()->create()->id,
            'uuid' => Str::uuid()
        ]);
    }

    public function test_given_negative_starting_balance_when_creating_account_it_will_cause_database_excepction(): void {
        $this->expectException(QueryException::class);

        Account::create([
            'user_id' => User::factory()->create()->id,
            'currency_id' => Currency::factory()->create()->id,
            'uuid' => Str::uuid(),
            'balance' => -1
        ]);
    }
}

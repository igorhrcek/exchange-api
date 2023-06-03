<?php

namespace Tests\Unit;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class ExchangeRateTest extends TestCase
{
    public function test_given_all_correct_necessary_information_when_creating_exchange_rate_it_will_be_created(): void {
        ExchangeRate::create([
            'from_currency_id' => Currency::factory()->create()->id,
            'to_currency_id' => Currency::factory()->create()->id,
            'rate' => '1.1'
        ]);

        $this->assertDatabaseCount('exchange_rates', 1);
    }

    public function test_given_two_same_currencies_when_creating_exchange_rate_it_cause_database_exception(): void {
        $exchangeRate = ExchangeRate::factory()->create();

        $this->expectException(QueryException::class);

        ExchangeRate::create([
            'from_currency_id' => $exchangeRate->from_currency_id,
            'to_currency_id' => $exchangeRate->to_currency_id,
            'rate' => '1.1'
        ]);
    }

    public function test_given_one_of_currencies_is_missing_when_creating_exchange_rate_it_cause_database_exception(): void {
        $this->expectException(QueryException::class);

        ExchangeRate::create([
            'to_currency_id' => Currency::factory()->create(),
            'rate' => '1.1'
        ]);
    }

    public function test_given_that_rate_is_missing_when_creating_exchange_rate_it_cause_database_exception(): void {
        $this->expectException(QueryException::class);

        ExchangeRate::create([
            'from_currency_id' => Currency::factory()->create(),
            'to_currency_id' => Currency::factory()->create()
        ]);
    }
}

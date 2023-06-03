<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Currency;
use Illuminate\Database\QueryException;

use function PHPUnit\Framework\assertEquals;

class CurrencyTest extends TestCase
{
    public function test_given_currency_code_when_creating_currency_it_will_be_created(): void {
        $currency = Currency::create([
            'code' => 'RSD'
        ]);

        $this->assertDatabaseCount('currencies', 1);
    }

    public function test_given_existing_currency_code_when_creating_currency_it_will_cause_database_exception(): void {
        $currency = Currency::factory()->create();

        $this->expectException(QueryException::class);
        Currency::create([
            'code' => $currency->code
        ]);
    }

    public function test_given_currency_code_longer_than_three_characters_when_creating_curency_it_will_cause_database_exception(): void {
        $this->expectException(QueryException::class);

        $currency = Currency::create([
            'code' => 'ABCD'
        ]);
    }
}

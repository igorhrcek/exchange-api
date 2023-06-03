<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker): void
    {
        ExchangeRate::truncate();

        $currencies = Currency::select('id')->get();

        foreach($currencies as $fromCurrency) {
            foreach($currencies as $toCurrency) {
                //Prevent same currency exchange rate
                if($fromCurrency === $toCurrency) {
                    continue;
                }

                ExchangeRate::create([
                    'from_currency_id' => $fromCurrency->id,
                    'to_currency_id' => $toCurrency->id,
                    'rate' => $faker->randomFloat(2, 1, 5)
                ]);
            }
        }
    }
}

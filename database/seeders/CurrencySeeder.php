<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CurrencySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::truncate();

        $currencies = ['EUR', 'USD', 'JPY', 'GBP', 'CHF'];

        foreach($currencies as $currency) {
            DB::table('currencies')->insert(
                [
                    'code' => $currency
                ]
            );
        }
    }
}

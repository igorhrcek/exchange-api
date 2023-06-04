<?php

namespace App\Services;

class ExchangeService {
    /**
     * Calculates amount based on conversion rate
     *
     * @param float $amount
     * @param float $conversionRate
     * @return float
     */
    public static function convert(float $amount, float $conversionRate): float {
        return $amount * $conversionRate;
    }
}

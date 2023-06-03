<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Currency extends Model
{
    use HasFactory;

    /**
     * Get all accounts with a given currency
     *
     * @return BelongsToMany
     */
    public function accounts(): BelongsToMany {
        return $this->belongsToMany(Account::class);
    }

    /**
     * Define relation between currency and exchange rate
     *
     * @return HasOne
     */
    public function exchangeRate(): HasOne {
        return $this->hasOne(ExchangeRate::class);
    }
}

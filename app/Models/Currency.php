<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Currency extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code'
    ];

    /**
     * Auto load relations
     *
     * @var array
     */
    protected $with = array('exchangeRate');

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
    public function exchangeRate(): HasMany {
        return $this->hasMany(ExchangeRate::class, 'from_currency_id');
    }
}

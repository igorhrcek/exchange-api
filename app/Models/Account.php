<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Account extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'currency_id',
        'uuid',
        'balance'
    ];

    /**
     * Auto load relations
     *
     * @var array
     */
    protected $with = array('currency', 'transactions');

    /**
     * Define primary key
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Get the user that owns the account
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Define a relation between account and  a currency
     *
     * @return HasOne
     */
    public function currency(): HasOne {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

    /**
     * Define relation between account and transactions
     *
     * @return HasMany
     */
    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}

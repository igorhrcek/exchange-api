<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'amount',
        'reference'
    ];

    /**
     * Get all accounts with a given currency
     *
     * @return BelongsToMany
     */
    public function account(): BelongsTo {
        return $this->belongsTo(Account::class);
    }
}

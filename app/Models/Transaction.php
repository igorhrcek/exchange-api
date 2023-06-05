<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

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

    /**
     * Disable updated_at timestamping
     *
     * @param mixed $value
     * @return void
     */
    public function setUpdatedAt($value) {
        //
    }

    /**
     * Scope a query to return transactions that belong to a reference
     *
     * @param Builder $query
     * @param string $reference
     * @return void
     */
    public function scopeReference(Builder $query, string $reference): void {
        $query->where('reference', '=', $reference);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'reference';
    }
}

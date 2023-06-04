<?php

namespace App\Rules;

use Closure;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\ValidationRule;

class AccountWithCurrencyExistRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = Auth::user();

        if (Account::where('currency_id', '=', $value)->where('user_id', '=', $user->id)->count() !== 0) {
            $fail('Account with this currency already exist.');
        }
    }
}

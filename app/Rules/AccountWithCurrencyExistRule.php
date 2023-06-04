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

        if (collect($user->accounts)->contains('currency_id', $value)) {
            $fail('Account with this currency already exist.');
        }
    }
}

<?php

namespace App\Rules;

use Closure;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\ValidationRule;

class AccountOwnershipRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = Auth::user();

        if (!collect($user->accounts)->contains('id', $value)) {
            $fail('The account belongs to another user. You are not authorized to do this transaction.');
        }
    }
}

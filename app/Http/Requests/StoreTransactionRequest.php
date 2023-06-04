<?php

namespace App\Http\Requests;

use App\Rules\AccountOwnershipRule;
use App\Rules\ExchangeTransationAmountRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'source_account_id' => ["bail", "required", "exists:accounts,id", new AccountOwnershipRule],
            'destination_account_id' => ["bail", "required", "exists:accounts,id", "different:source_account_id", new AccountOwnershipRule],
            'amount' => ["bail", "required", "numeric", new ExchangeTransationAmountRule]
        ];
    }

    /**
     * Custom error messages
     *
     * @return array
     */
    public function messages(): array {
        return [
            'source_account_id.required' => 'Source account is required',
            'source_account_id.exists' => 'Provided account does not exist',
            'destination_account_id.required' => 'Source account is required',
            'destination_account_id.exists' => 'Provided account does not exist',
            'destination_account_id.different' => 'Destination account cannot be same as source account',
            'amount.required' => 'Transfer amount is required',
            'amount.decimal' => 'Transfer amount must be a number'
        ];
    }
}

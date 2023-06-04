<?php

namespace App\Http\Requests;

use App\Rules\AccountWithCurrencyExistRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreAccountRequest extends FormRequest
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
            'currency_id' => ["required", "exists:currencies,id", new AccountWithCurrencyExistRule]
        ];
    }

    /**
     * Custom error messages
     *
     * @return array
     */
    public function messages(): array {
        return [
            'currency_id.required' => 'Currency is required',
            'currency_id.exists' => 'Provided currency does not exist'
        ];
    }
}

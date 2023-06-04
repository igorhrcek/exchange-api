<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreUserRequest extends FormRequest
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
            'email' => 'required|unique:users|email',
            'name' => 'required|regex:/^[A-Za-z ]+$/'
        ];
    }

    /**
     * Return error if validation fails
     *
     * @param Validator $validator
     * @return void
     */
    public function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }

    /**
     * Custom error messages
     *
     * @return array
     */
    public function messages(): array {
        return [
            'email.required' => 'Email address is required',
            'email.unique' => 'Email address was already used for another account',
            'email.email' => 'Provided email address is not a valid address',
            'name.required' => 'Full name must be provided',
            'name.regex' => 'Full name can contain only letters and spaces'
        ];
    }
}

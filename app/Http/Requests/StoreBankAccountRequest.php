<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->canManageAccounts();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'account_type_id' => 'required|exists:account_types,id',
            'balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a customer for this account.',
            'user_id.exists' => 'The selected customer is invalid.',
            'account_type_id.required' => 'Please select an account type.',
            'account_type_id.exists' => 'The selected account type is invalid.',
            'balance.required' => 'Initial balance is required.',
            'balance.numeric' => 'Balance must be a valid number.',
            'balance.min' => 'Balance cannot be negative.',
        ];
    }
}
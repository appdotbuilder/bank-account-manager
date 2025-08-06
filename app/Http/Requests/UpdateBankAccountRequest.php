<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBankAccountRequest extends FormRequest
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
            'account_type_id' => 'required|exists:account_types,id',
            'status' => 'required|in:active,blocked,dormant,closed',
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
            'account_type_id.required' => 'Please select an account type.',
            'account_type_id.exists' => 'The selected account type is invalid.',
            'status.required' => 'Account status is required.',
            'status.in' => 'Invalid account status selected.',
        ];
    }
}
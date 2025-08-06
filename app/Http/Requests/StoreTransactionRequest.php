<?php

namespace App\Http\Requests;

use App\Models\BankAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:debit,credit,transfer',
            'from_account_id' => [
                'required_if:type,debit,transfer',
                'exists:bank_accounts,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $account = BankAccount::find($value);
                        if ($account && auth()->user()->isCustomer() && $account->user_id !== auth()->id()) {
                            $fail('You can only transact from your own accounts.');
                        }
                        if ($account && $account->status !== 'active') {
                            $fail('Cannot transact from a non-active account.');
                        }
                    }
                },
            ],
            'to_account_id' => [
                'required_if:type,credit,transfer',
                'exists:bank_accounts,id',
                'different:from_account_id',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    if ($this->from_account_id) {
                        $account = BankAccount::with('accountType')->find($this->from_account_id);
                        if ($account) {
                            // Check per transaction limit
                            if ($account->accountType->per_transaction_limit && $value > $account->accountType->per_transaction_limit) {
                                $fail('Amount exceeds per transaction limit of ' . $account->accountType->per_transaction_limit);
                            }
                            
                            // Check available balance
                            $availableBalance = $account->getAvailableBalance();
                            if (in_array($this->type, ['debit', 'transfer']) && $value > $availableBalance) {
                                $fail('Insufficient available balance. Available: ' . $availableBalance);
                            }
                        }
                    }
                },
            ],
            'fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:255',
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
            'type.required' => 'Transaction type is required.',
            'type.in' => 'Invalid transaction type.',
            'from_account_id.required_if' => 'Source account is required for debit and transfer transactions.',
            'to_account_id.required_if' => 'Destination account is required for credit and transfer transactions.',
            'to_account_id.different' => 'Source and destination accounts must be different.',
            'amount.required' => 'Transaction amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be greater than 0.',
        ];
    }
}
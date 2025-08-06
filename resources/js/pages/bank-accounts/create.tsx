import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';

interface AccountType {
    id: number;
    name: string;
    description: string;
    min_balance: string;
    max_balance: string | null;
}

interface Customer {
    id: number;
    name: string;
    email: string;
}

interface Props {
    accountTypes: AccountType[];
    customers: Customer[];
    [key: string]: unknown;
}

export default function CreateBankAccount({ accountTypes, customers }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        user_id: '',
        account_type_id: '',
        balance: '0.00',
        notes: '',
    });

    const [selectedAccountType, setSelectedAccountType] = useState<AccountType | null>(null);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/bank-accounts');
    };

    const handleAccountTypeChange = (value: string) => {
        setData('account_type_id', value);
        const accountType = accountTypes.find(type => type.id.toString() === value);
        setSelectedAccountType(accountType || null);
        
        if (accountType) {
            setData('balance', accountType.min_balance);
        }
    };

    return (
        <AppShell>
            <Head title="Create Bank Account" />
            
            <div className="space-y-6">
                <div className="flex justify-between items-center">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Create Bank Account</h1>
                        <p className="text-gray-600">Create a new bank account for a customer</p>
                    </div>
                    
                    <Button asChild variant="outline">
                        <Link href="/bank-accounts">Cancel</Link>
                    </Button>
                </div>

                <div className="bg-white rounded-lg shadow p-6">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <Label htmlFor="user_id">Customer *</Label>
                                <Select
                                    value={data.user_id}
                                    onValueChange={(value) => setData('user_id', value)}
                                    name="user_id"
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select a customer" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {customers.map((customer) => (
                                            <SelectItem key={customer.id} value={customer.id.toString()}>
                                                {customer.name} ({customer.email})
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.user_id && (
                                    <p className="text-sm text-red-600">{errors.user_id}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="account_type_id">Account Type *</Label>
                                <Select
                                    value={data.account_type_id}
                                    onValueChange={handleAccountTypeChange}
                                    name="account_type_id"
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select account type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {accountTypes.map((type) => (
                                            <SelectItem key={type.id} value={type.id.toString()}>
                                                {type.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.account_type_id && (
                                    <p className="text-sm text-red-600">{errors.account_type_id}</p>
                                )}
                                
                                {selectedAccountType && (
                                    <div className="text-sm text-gray-600 bg-blue-50 p-3 rounded-lg">
                                        <p><strong>Description:</strong> {selectedAccountType.description}</p>
                                        <p><strong>Min Balance:</strong> ${selectedAccountType.min_balance}</p>
                                        {selectedAccountType.max_balance && (
                                            <p><strong>Max Balance:</strong> ${selectedAccountType.max_balance}</p>
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="balance">Initial Balance *</Label>
                            <Input
                                id="balance"
                                name="balance"
                                type="number"
                                step="0.01"
                                min="0"
                                value={data.balance}
                                onChange={(e) => setData('balance', e.target.value)}
                                placeholder="0.00"
                                required
                            />
                            {errors.balance && (
                                <p className="text-sm text-red-600">{errors.balance}</p>
                            )}
                            {selectedAccountType && (
                                <p className="text-sm text-gray-500">
                                    Minimum balance required: ${selectedAccountType.min_balance}
                                </p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="notes">Notes</Label>
                            <Textarea
                                id="notes"
                                name="notes"
                                value={data.notes}
                                onChange={(e) => setData('notes', e.target.value)}
                                placeholder="Optional notes about this account..."
                                rows={3}
                            />
                            {errors.notes && (
                                <p className="text-sm text-red-600">{errors.notes}</p>
                            )}
                        </div>

                        <div className="flex justify-end space-x-4">
                            <Button type="button" variant="outline" asChild>
                                <Link href="/bank-accounts">Cancel</Link>
                            </Button>
                            <Button type="submit" disabled={processing}>
                                {processing ? 'Creating...' : 'Create Account'}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </AppShell>
    );
}
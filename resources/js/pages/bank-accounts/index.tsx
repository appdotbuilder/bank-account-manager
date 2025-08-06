import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { formatCurrency } from '@/lib/utils';

interface BankAccount {
    id: number;
    account_number: string;
    balance: string;
    status: string;
    user?: {
        name: string;
        email: string;
    };
    account_type: {
        name: string;
    };
    created_at: string;
}

interface Props {
    accounts: {
        data: BankAccount[];
        links: Record<string, unknown>[];
        meta: Record<string, unknown>;
    };
    userRole: string;
    [key: string]: unknown;
}

const statusColors = {
    active: 'bg-green-100 text-green-800',
    blocked: 'bg-red-100 text-red-800',
    dormant: 'bg-yellow-100 text-yellow-800',
    closed: 'bg-gray-100 text-gray-800',
};

export default function BankAccountsIndex({ accounts, userRole }: Props) {
    return (
        <AppShell>
            <Head title="Bank Accounts" />
            
            <div className="space-y-6">
                <div className="flex justify-between items-center">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">
                            {userRole === 'customer' ? 'My Bank Accounts' : 'Bank Accounts'}
                        </h1>
                        <p className="text-gray-600">
                            {userRole === 'customer' 
                                ? 'Manage your personal bank accounts' 
                                : 'Manage all customer bank accounts'}
                        </p>
                    </div>
                    
                    {userRole !== 'customer' && (
                        <Button asChild>
                            <Link href="/bank-accounts/create">
                                Create New Account
                            </Link>
                        </Button>
                    )}
                </div>

                <div className="bg-white rounded-lg shadow">
                    <div className="overflow-hidden">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Account
                                    </th>
                                    {userRole !== 'customer' && (
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Customer
                                        </th>
                                    )}
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Balance
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {accounts.data.map((account) => (
                                    <tr key={account.id} className="hover:bg-gray-50">
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div className="text-sm font-medium text-gray-900">
                                                    {account.account_number}
                                                </div>
                                                <div className="text-sm text-gray-500">
                                                    ID: {account.id}
                                                </div>
                                            </div>
                                        </td>
                                        
                                        {userRole !== 'customer' && (
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div>
                                                    <div className="text-sm font-medium text-gray-900">
                                                        {account.user?.name}
                                                    </div>
                                                    <div className="text-sm text-gray-500">
                                                        {account.user?.email}
                                                    </div>
                                                </div>
                                            </td>
                                        )}
                                        
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="text-sm text-gray-900">
                                                {account.account_type.name}
                                            </div>
                                        </td>
                                        
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="text-sm font-medium text-gray-900">
                                                {formatCurrency(parseFloat(account.balance))}
                                            </div>
                                        </td>
                                        
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <Badge className={statusColors[account.status as keyof typeof statusColors]}>
                                                {account.status}
                                            </Badge>
                                        </td>
                                        
                                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div className="flex justify-end space-x-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    asChild
                                                >
                                                    <Link href={`/bank-accounts/${account.id}`}>
                                                        View
                                                    </Link>
                                                </Button>
                                                
                                                {userRole === 'customer' && account.status === 'active' && (
                                                    <Button
                                                        size="sm"
                                                        asChild
                                                    >
                                                        <Link href={`/transactions/create?from_account_id=${account.id}`}>
                                                            Transfer
                                                        </Link>
                                                    </Button>
                                                )}
                                                
                                                {userRole !== 'customer' && (
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                        asChild
                                                    >
                                                        <Link href={`/bank-accounts/${account.id}/edit`}>
                                                            Edit
                                                        </Link>
                                                    </Button>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    
                    {accounts.data.length === 0 && (
                        <div className="text-center py-12">
                            <div className="text-gray-500 text-lg mb-4">No bank accounts found</div>
                            {userRole !== 'customer' && (
                                <Button asChild>
                                    <Link href="/bank-accounts/create">
                                        Create First Account
                                    </Link>
                                </Button>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </AppShell>
    );
}
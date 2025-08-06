import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { formatCurrency, formatDateTime } from '@/lib/utils';

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
}

interface Transaction {
    id: number;
    transaction_id: string;
    type: string;
    amount: string;
    status: string;
    description?: string;
    created_at: string;
    from_account?: {
        account_number: string;
        user?: {
            name: string;
        };
    };
    to_account?: {
        account_number: string;
        user?: {
            name: string;
        };
    };
}

interface Props {
    userRole: string;
    stats: {
        totalBalance?: number;
        activeAccounts?: number;
        totalAccounts?: number;
        recentTransactions?: number;
        totalCustomers?: number;
        todayTransactions?: number;
        pendingTransactions?: number;
    };
    accounts?: BankAccount[];
    recentTransactions?: Transaction[];
    recentAccounts?: BankAccount[];
    [key: string]: unknown;
}

const statusColors = {
    active: 'bg-green-100 text-green-800',
    blocked: 'bg-red-100 text-red-800',
    dormant: 'bg-yellow-100 text-yellow-800',
    closed: 'bg-gray-100 text-gray-800',
    completed: 'bg-green-100 text-green-800',
    pending: 'bg-yellow-100 text-yellow-800',
    failed: 'bg-red-100 text-red-800',
    cancelled: 'bg-gray-100 text-gray-800',
};

export default function Dashboard({ userRole, stats, accounts, recentTransactions }: Props) {
    return (
        <AppShell>
            <Head title="Dashboard" />
            
            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">
                        {userRole === 'customer' ? 'My Banking Dashboard' : 'Banking System Dashboard'}
                    </h1>
                    <p className="text-gray-600">
                        {userRole === 'customer' 
                            ? 'Overview of your accounts and recent activity' 
                            : 'System-wide banking overview and management'}
                    </p>
                </div>

                {/* Stats Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    {userRole === 'customer' ? (
                        <>
                            <div className="bg-white rounded-lg shadow p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <span className="text-blue-600">💰</span>
                                        </div>
                                    </div>
                                    <div className="ml-4">
                                        <p className="text-sm font-medium text-gray-600">Total Balance</p>
                                        <p className="text-lg font-bold text-gray-900">
                                            {stats.totalBalance !== undefined ? formatCurrency(stats.totalBalance) : '$0.00'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="bg-white rounded-lg shadow p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                            <span className="text-green-600">🏦</span>
                                        </div>
                                    </div>
                                    <div className="ml-4">
                                        <p className="text-sm font-medium text-gray-600">Active Accounts</p>
                                        <p className="text-lg font-bold text-gray-900">{stats.activeAccounts || 0}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="bg-white rounded-lg shadow p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <span className="text-purple-600">💳</span>
                                        </div>
                                    </div>
                                    <div className="ml-4">
                                        <p className="text-sm font-medium text-gray-600">Total Accounts</p>
                                        <p className="text-lg font-bold text-gray-900">{stats.totalAccounts || 0}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="bg-white rounded-lg shadow p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                            <span className="text-yellow-600">📊</span>
                                        </div>
                                    </div>
                                    <div className="ml-4">
                                        <p className="text-sm font-medium text-gray-600">Recent Transactions</p>
                                        <p className="text-lg font-bold text-gray-900">{stats.recentTransactions || 0}</p>
                                    </div>
                                </div>
                            </div>
                        </>
                    ) : (
                        <>
                            <div className="bg-white rounded-lg shadow p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <span className="text-blue-600">👥</span>
                                        </div>
                                    </div>
                                    <div className="ml-4">
                                        <p className="text-sm font-medium text-gray-600">Total Customers</p>
                                        <p className="text-lg font-bold text-gray-900">{stats.totalCustomers || 0}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="bg-white rounded-lg shadow p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                            <span className="text-green-600">🏦</span>
                                        </div>
                                    </div>
                                    <div className="ml-4">
                                        <p className="text-sm font-medium text-gray-600">Total Accounts</p>
                                        <p className="text-lg font-bold text-gray-900">{stats.totalAccounts || 0}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="bg-white rounded-lg shadow p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <span className="text-purple-600">💰</span>
                                        </div>
                                    </div>
                                    <div className="ml-4">
                                        <p className="text-sm font-medium text-gray-600">System Balance</p>
                                        <p className="text-lg font-bold text-gray-900">
                                            {stats.totalBalance !== undefined ? formatCurrency(stats.totalBalance) : '$0.00'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="bg-white rounded-lg shadow p-6">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                            <span className="text-yellow-600">📊</span>
                                        </div>
                                    </div>
                                    <div className="ml-4">
                                        <p className="text-sm font-medium text-gray-600">Today's Transactions</p>
                                        <p className="text-lg font-bold text-gray-900">{stats.todayTransactions || 0}</p>
                                    </div>
                                </div>
                            </div>
                        </>
                    )}
                </div>

                {/* Quick Actions */}
                <div className="bg-white rounded-lg shadow p-6">
                    <h2 className="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                    <div className="flex flex-wrap gap-4">
                        <Button asChild>
                            <Link href="/bank-accounts">
                                {userRole === 'customer' ? 'My Accounts' : 'All Accounts'}
                            </Link>
                        </Button>
                        
                        <Button asChild variant="outline">
                            <Link href="/transactions">
                                {userRole === 'customer' ? 'My Transactions' : 'All Transactions'}
                            </Link>
                        </Button>
                        
                        {userRole === 'customer' && (
                            <Button asChild variant="outline">
                                <Link href="/transactions/create">New Transfer</Link>
                            </Button>
                        )}
                        
                        {userRole !== 'customer' && (
                            <Button asChild variant="outline">
                                <Link href="/bank-accounts/create">Create Account</Link>
                            </Button>
                        )}
                    </div>
                </div>

                {/* Customer Accounts or Recent Accounts */}
                {accounts && accounts.length > 0 && (
                    <div className="bg-white rounded-lg shadow">
                        <div className="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h2 className="text-lg font-semibold text-gray-900">
                                {userRole === 'customer' ? 'My Accounts' : 'Recent Accounts'}
                            </h2>
                            <Button asChild variant="outline" size="sm">
                                <Link href="/bank-accounts">View All</Link>
                            </Button>
                        </div>
                        <div className="overflow-hidden">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Account
                                        </th>
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
                                    {accounts.map((account) => (
                                        <tr key={account.id} className="hover:bg-gray-50">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm font-medium text-gray-900">
                                                    {account.account_number}
                                                </div>
                                            </td>
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
                                                <Button variant="outline" size="sm" asChild>
                                                    <Link href={`/bank-accounts/${account.id}`}>
                                                        View
                                                    </Link>
                                                </Button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                )}

                {/* Recent Transactions */}
                {recentTransactions && recentTransactions.length > 0 && (
                    <div className="bg-white rounded-lg shadow">
                        <div className="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h2 className="text-lg font-semibold text-gray-900">Recent Transactions</h2>
                            <Button asChild variant="outline" size="sm">
                                <Link href="/transactions">View All</Link>
                            </Button>
                        </div>
                        <div className="overflow-hidden">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Transaction
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Amount
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {recentTransactions.map((transaction) => (
                                        <tr key={transaction.id} className="hover:bg-gray-50">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm font-medium text-gray-900">
                                                    {transaction.transaction_id}
                                                </div>
                                                {transaction.description && (
                                                    <div className="text-sm text-gray-500">
                                                        {transaction.description}
                                                    </div>
                                                )}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm text-gray-900 capitalize">
                                                    {transaction.type}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm font-medium text-gray-900">
                                                    {formatCurrency(parseFloat(transaction.amount))}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <Badge className={statusColors[transaction.status as keyof typeof statusColors]}>
                                                    {transaction.status}
                                                </Badge>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm text-gray-900">
                                                    {formatDateTime(transaction.created_at)}
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                )}
            </div>
        </AppShell>
    );
}
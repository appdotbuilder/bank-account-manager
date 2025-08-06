<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\User;
use App\Models\AccountType;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isCustomer()) {
            return $this->customerDashboard($user);
        } else {
            return $this->adminDashboard($user);
        }
    }

    /**
     * Customer dashboard with their accounts and transactions.
     */
    protected function customerDashboard($user)
    {
        $accounts = BankAccount::where('user_id', $user->id)
            ->with('accountType')
            ->get();
        
        $recentTransactions = Transaction::where(function ($query) use ($user) {
                $accountIds = $user->bankAccounts()->pluck('id')->toArray();
                $query->whereIn('from_account_id', $accountIds)
                      ->orWhereIn('to_account_id', $accountIds);
            })
            ->with(['fromAccount', 'toAccount'])
            ->latest()
            ->limit(5)
            ->get();
        
        $totalBalance = $accounts->sum('balance');
        $activeAccounts = $accounts->where('status', 'active')->count();
        
        return Inertia::render('dashboard', [
            'userRole' => $user->role,
            'stats' => [
                'totalBalance' => $totalBalance,
                'activeAccounts' => $activeAccounts,
                'totalAccounts' => $accounts->count(),
                'recentTransactions' => $recentTransactions->count(),
            ],
            'accounts' => $accounts,
            'recentTransactions' => $recentTransactions,
        ]);
    }

    /**
     * Admin/Operator dashboard with system statistics.
     */
    protected function adminDashboard($user)
    {
        $totalCustomers = User::customers()->count();
        $totalAccounts = BankAccount::count();
        $activeAccounts = BankAccount::where('status', 'active')->count();
        $totalBalance = BankAccount::sum('balance');
        $todayTransactions = Transaction::whereDate('created_at', today())->count();
        $pendingTransactions = Transaction::where('status', 'pending')->count();
        
        $recentTransactions = Transaction::with(['fromAccount.user', 'toAccount.user'])
            ->latest()
            ->limit(10)
            ->get();
        
        $recentAccounts = BankAccount::with(['user', 'accountType'])
            ->latest()
            ->limit(5)
            ->get();
        
        return Inertia::render('dashboard', [
            'userRole' => $user->role,
            'stats' => [
                'totalCustomers' => $totalCustomers,
                'totalAccounts' => $totalAccounts,
                'activeAccounts' => $activeAccounts,
                'totalBalance' => $totalBalance,
                'todayTransactions' => $todayTransactions,
                'pendingTransactions' => $pendingTransactions,
            ],
            'recentTransactions' => $recentTransactions,
            'recentAccounts' => $recentAccounts,
        ]);
    }
}
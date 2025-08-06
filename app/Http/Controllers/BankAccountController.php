<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBankAccountRequest;
use App\Http\Requests\UpdateBankAccountRequest;
use App\Models\BankAccount;
use App\Models\AccountType;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isCustomer()) {
            $accounts = BankAccount::where('user_id', $user->id)
                ->with(['accountType'])
                ->latest()
                ->paginate(10);
        } else {
            $accounts = BankAccount::with(['user', 'accountType'])
                ->latest()
                ->paginate(10);
        }
        
        return Inertia::render('bank-accounts/index', [
            'accounts' => $accounts,
            'userRole' => $user->role,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        if ($user->isCustomer()) {
            return redirect()->route('bank-accounts.index')
                ->with('error', 'Only administrators and operators can create accounts.');
        }
        
        $accountTypes = AccountType::active()->get();
        $customers = User::customers()->get();
        
        return Inertia::render('bank-accounts/create', [
            'accountTypes' => $accountTypes,
            'customers' => $customers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBankAccountRequest $request)
    {
        $user = auth()->user();
        
        if ($user->isCustomer()) {
            return redirect()->route('bank-accounts.index')
                ->with('error', 'Only administrators and operators can create accounts.');
        }
        
        $data = $request->validated();
        $data['account_number'] = BankAccount::generateAccountNumber();
        $data['last_activity_at'] = now();
        
        $account = BankAccount::create($data);
        
        return redirect()->route('bank-accounts.show', $account)
            ->with('success', 'Bank account created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount)
    {
        $user = auth()->user();
        
        if ($user->isCustomer() && $bankAccount->user_id !== $user->id) {
            return redirect()->route('bank-accounts.index')
                ->with('error', 'You can only view your own accounts.');
        }
        
        $bankAccount->load(['user', 'accountType', 'holds.createdBy', 'autoDebits']);
        
        // Get recent transactions
        $fromTransactions = $bankAccount->fromTransactions()
            ->with(['fromAccount', 'toAccount'])
            ->latest()
            ->limit(5)
            ->get();
            
        $toTransactions = $bankAccount->toTransactions()
            ->with(['fromAccount', 'toAccount'])
            ->latest()
            ->limit(5)
            ->get();
            
        $transactions = $fromTransactions->merge($toTransactions)
            ->sortByDesc('created_at')
            ->take(10);
        
        return Inertia::render('bank-accounts/show', [
            'account' => $bankAccount,
            'transactions' => $transactions,
            'userRole' => $user->role,
            'availableBalance' => $bankAccount->getAvailableBalance(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount)
    {
        $user = auth()->user();
        
        if ($user->isCustomer()) {
            return redirect()->route('bank-accounts.index')
                ->with('error', 'Only administrators and operators can edit accounts.');
        }
        
        $accountTypes = AccountType::active()->get();
        $bankAccount->load(['user', 'accountType']);
        
        return Inertia::render('bank-accounts/edit', [
            'account' => $bankAccount,
            'accountTypes' => $accountTypes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBankAccountRequest $request, BankAccount $bankAccount)
    {
        $user = auth()->user();
        
        if ($user->isCustomer()) {
            return redirect()->route('bank-accounts.index')
                ->with('error', 'Only administrators and operators can edit accounts.');
        }
        
        $bankAccount->update($request->validated());
        
        return redirect()->route('bank-accounts.show', $bankAccount)
            ->with('success', 'Bank account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount)
    {
        $user = auth()->user();
        
        if (!$user->isAdministrator()) {
            return redirect()->route('bank-accounts.index')
                ->with('error', 'Only administrators can delete accounts.');
        }
        
        $bankAccount->update(['status' => 'closed', 'closed_at' => now()]);
        
        return redirect()->route('bank-accounts.index')
            ->with('success', 'Bank account closed successfully.');
    }
}
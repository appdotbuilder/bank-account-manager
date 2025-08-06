<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $accountId = $request->get('account_id');
        
        $query = Transaction::with(['fromAccount.user', 'toAccount.user', 'processedBy'])
            ->latest();
        
        if ($user->isCustomer()) {
            $userAccountIds = $user->bankAccounts()->pluck('id')->toArray();
            $query->where(function ($q) use ($userAccountIds) {
                $q->whereIn('from_account_id', $userAccountIds)
                  ->orWhereIn('to_account_id', $userAccountIds);
            });
        }
        
        if ($accountId) {
            $query->where(function ($q) use ($accountId) {
                $q->where('from_account_id', $accountId)
                  ->orWhere('to_account_id', $accountId);
            });
        }
        
        $transactions = $query->paginate(20);
        
        return Inertia::render('transactions/index', [
            'transactions' => $transactions,
            'userRole' => $user->role,
            'accountId' => $accountId,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $accountId = $request->get('from_account_id');
        
        $accounts = $user->isCustomer() 
            ? $user->bankAccounts()->where('status', 'active')->with('accountType')->get()
            : BankAccount::where('status', 'active')->with(['user', 'accountType'])->get();
        
        $fromAccount = $accountId ? BankAccount::find($accountId) : null;
        
        return Inertia::render('transactions/create', [
            'accounts' => $accounts,
            'fromAccount' => $fromAccount,
            'userRole' => $user->role,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();
        
        DB::transaction(function () use ($data, $user) {
            $fromAccount = BankAccount::lockForUpdate()->find($data['from_account_id']);
            $toAccount = $data['to_account_id'] ? BankAccount::lockForUpdate()->find($data['to_account_id']) : null;
            
            // Create transaction record
            $transaction = Transaction::create([
                'transaction_id' => Transaction::generateTransactionId(),
                'from_account_id' => $data['from_account_id'],
                'to_account_id' => $data['to_account_id'] ?? null,
                'type' => $data['type'],
                'amount' => $data['amount'],
                'fee' => $data['fee'] ?? 0,
                'description' => $data['description'] ?? null,
                'status' => 'completed',
                'processed_by' => $user->id,
            ]);
            
            // Update account balances
            if ($data['type'] === 'debit' || $data['type'] === 'transfer') {
                $fromAccount->decrement('balance', $data['amount'] + ($data['fee'] ?? 0));
                $fromAccount->update(['last_activity_at' => now()]);
            }
            
            if (($data['type'] === 'credit' || $data['type'] === 'transfer') && $toAccount) {
                $toAccount->increment('balance', $data['amount']);
                $toAccount->update(['last_activity_at' => now()]);
                
                // Reactivate dormant account if credit transaction
                if ($toAccount->status === 'dormant' && $toAccount->accountType->reactivate_on_credit) {
                    $toAccount->update(['status' => 'active', 'dormant_at' => null]);
                }
            }
        });
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction completed successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        $user = auth()->user();
        
        // Check if customer can view this transaction
        if ($user->isCustomer()) {
            $userAccountIds = $user->bankAccounts()->pluck('id')->toArray();
            if (!in_array($transaction->from_account_id, $userAccountIds) && 
                !in_array($transaction->to_account_id, $userAccountIds)) {
                return redirect()->route('transactions.index')
                    ->with('error', 'You can only view your own transactions.');
            }
        }
        
        $transaction->load(['fromAccount.user', 'toAccount.user', 'processedBy']);
        
        return Inertia::render('transactions/show', [
            'transaction' => $transaction,
            'userRole' => $user->role,
        ]);
    }
}
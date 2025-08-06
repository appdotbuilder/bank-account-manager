<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique()->comment('Unique transaction identifier');
            $table->foreignId('from_account_id')->nullable()->constrained('bank_accounts')->onDelete('cascade');
            $table->foreignId('to_account_id')->nullable()->constrained('bank_accounts')->onDelete('cascade');
            $table->enum('type', ['debit', 'credit', 'transfer'])->comment('Transaction type');
            $table->decimal('amount', 15, 2)->comment('Transaction amount');
            $table->decimal('fee', 15, 2)->default(0)->comment('Transaction fee');
            $table->string('description')->nullable()->comment('Transaction description');
            $table->string('reference')->nullable()->comment('External reference number');
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->json('metadata')->nullable()->comment('Additional transaction data');
            $table->timestamps();
            
            $table->index(['from_account_id', 'created_at']);
            $table->index(['to_account_id', 'created_at']);
            $table->index('transaction_id');
            $table->index(['type', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
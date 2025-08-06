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
        Schema::create('auto_debits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->onDelete('cascade');
            $table->string('name')->comment('Name/description of the auto debit');
            $table->decimal('amount', 15, 2)->comment('Amount to debit');
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'yearly'])->comment('Debit frequency');
            $table->date('next_debit_date')->comment('Next scheduled debit date');
            $table->date('end_date')->nullable()->comment('When auto debit should stop');
            $table->boolean('is_active')->default(true)->comment('Whether auto debit is active');
            $table->foreignId('created_by')->constrained('users');
            $table->json('metadata')->nullable()->comment('Additional configuration data');
            $table->timestamps();
            
            $table->index(['bank_account_id', 'is_active']);
            $table->index('next_debit_date');
            $table->index(['is_active', 'next_debit_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_debits');
    }
};
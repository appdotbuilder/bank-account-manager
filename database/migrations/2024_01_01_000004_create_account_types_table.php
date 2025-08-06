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
        Schema::create('account_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Name of the account type');
            $table->text('description')->nullable()->comment('Description of the account type');
            $table->decimal('min_balance', 15, 2)->default(0)->comment('Minimum allowed balance');
            $table->decimal('max_balance', 15, 2)->nullable()->comment('Maximum allowed balance');
            $table->decimal('per_transaction_limit', 15, 2)->nullable()->comment('Maximum amount per transaction');
            $table->decimal('daily_transaction_limit', 15, 2)->nullable()->comment('Maximum daily transaction total');
            $table->integer('dormant_after_days')->nullable()->comment('Days of inactivity before account becomes dormant');
            $table->boolean('reactivate_on_credit')->default(true)->comment('Can dormant account be reactivated by credit transaction');
            $table->integer('auto_close_after_days')->nullable()->comment('Days of inactivity before auto closure');
            $table->boolean('is_active')->default(true)->comment('Whether this account type is available');
            $table->timestamps();
            
            $table->index('name');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_types');
    }
};
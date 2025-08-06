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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_number')->unique()->comment('Unique account number');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_type_id')->constrained();
            $table->decimal('balance', 15, 2)->default(0)->comment('Current account balance');
            $table->enum('status', ['active', 'blocked', 'dormant', 'closed'])->default('active');
            $table->timestamp('last_activity_at')->nullable()->comment('Last transaction timestamp');
            $table->timestamp('dormant_at')->nullable()->comment('When account became dormant');
            $table->timestamp('closed_at')->nullable()->comment('When account was closed');
            $table->text('notes')->nullable()->comment('Administrative notes');
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('account_number');
            $table->index('status');
            $table->index('last_activity_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
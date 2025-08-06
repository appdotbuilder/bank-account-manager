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
        Schema::create('account_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2)->comment('Amount to hold');
            $table->string('reason')->comment('Reason for the hold');
            $table->timestamp('expires_at')->nullable()->comment('When hold expires (null for indefinite)');
            $table->enum('status', ['active', 'released', 'expired'])->default('active');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('released_by')->nullable()->constrained('users');
            $table->timestamp('released_at')->nullable()->comment('When hold was released');
            $table->timestamps();
            
            $table->index(['bank_account_id', 'status']);
            $table->index('expires_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_holds');
    }
};
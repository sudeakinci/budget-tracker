<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('sender_account_id')->constrained('accounts')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            // $table->foreignId('receiver_account_id')->constrained('accounts')->onDelete('cascade');
            // $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->boolean('is_income');
            $table->text('description')->nullable();
            // $table->unsignedTinyInteger(column: 'transaction_type_id')->nullable();
            // $table->foreign('transaction_type_id')->references('id')->on('transaction_types');
            $table->date('date');
            $table->timestamps();
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
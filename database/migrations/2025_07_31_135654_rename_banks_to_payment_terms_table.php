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
        Schema::rename('banks', 'payment_terms');
        Schema::table('payment_terms', function (Blueprint $table) {
            $table->dropColumn(['country']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_terms', function (Blueprint $table) {
            $table->string('country', 100)->nullable();
        });
        Schema::rename('payment_terms', 'banks');
    }
};

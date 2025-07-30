<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('is_income')->default(false)->after('amount');
        });

        // Convert existing data
        DB::table('transactions')->where('type', 'income')->update(['is_income' => true]);
        DB::table('transactions')->where('type', 'outcome')->update(['is_income' => false]);

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('type')->after('amount');
        });

        // Convert existing data back
        DB::table('transactions')->where('is_income', true)->update(['type' => 'income']);
        DB::table('transactions')->where('is_income', false)->update(['type' => 'outcome']);

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('is_income');
        });
    }
};

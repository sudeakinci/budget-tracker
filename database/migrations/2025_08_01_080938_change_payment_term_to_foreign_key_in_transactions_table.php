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
        DB::table('transactions')->delete();

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('payment_term');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('payment_term_id')->after('user_id')->constrained('payment_terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['payment_term_id']);
            $table->dropColumn('payment_term_id');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_term')->after('user_id');
        });
    }
};

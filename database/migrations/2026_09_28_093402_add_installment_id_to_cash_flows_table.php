<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->foreignId('installment_id')
                ->nullable()
                ->after('loan_id')
                ->constrained('installments')
                ->nullOnDelete();

            $table->index('installment_id');
        });
    }

    public function down(): void
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->dropForeign(['installment_id']);
            $table->dropIndex(['installment_id']);
            $table->dropColumn('installment_id');
        });
    }
};

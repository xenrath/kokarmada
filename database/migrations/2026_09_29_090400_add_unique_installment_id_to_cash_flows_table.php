<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->dropForeign(['installment_id']);
            $table->dropIndex(['installment_id']);

            $table->unique('installment_id');

            $table->foreign('installment_id')
                ->references('id')
                ->on('installments')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->dropForeign(['installment_id']);
            $table->dropUnique(['installment_id']);

            $table->index('installment_id');

            $table->foreign('installment_id')
                ->references('id')
                ->on('installments')
                ->nullOnDelete();
        });
    }
};

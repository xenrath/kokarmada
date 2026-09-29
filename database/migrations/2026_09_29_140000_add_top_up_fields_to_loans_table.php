<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->string('loan_type', 20)->default('regular')->after('submitted_at');
            $table->foreignId('top_up_of_loan_id')->nullable()->after('loan_type')->constrained('loans')->restrictOnDelete();
            $table->decimal('top_up_amount', 15, 2)->nullable()->after('top_up_of_loan_id');
            $table->decimal('submitted_requested_amount', 15, 2)->nullable()->after('top_up_amount');
            $table->decimal('top_up_minimum_amount_snapshot', 15, 2)->nullable()->after('submitted_requested_amount');
            $table->decimal('loan_maximum_amount_snapshot', 15, 2)->nullable()->after('top_up_minimum_amount_snapshot');
            $table->decimal('declared_external_monthly_obligations', 15, 2)->default(0)->after('net_monthly_income');
            $table->text('declared_external_obligations_note')->nullable()->after('declared_external_monthly_obligations');
            $table->string('declared_external_obligations_proof')->nullable()->after('declared_external_obligations_note');
            $table->decimal('verified_net_monthly_income', 15, 2)->nullable()->after('declared_external_obligations_proof');
            $table->decimal('verified_other_monthly_income', 15, 2)->nullable()->after('verified_net_monthly_income');
            $table->decimal('verified_external_monthly_obligations', 15, 2)->nullable()->after('verified_other_monthly_income');

            $table->index('loan_type');
            $table->index('top_up_of_loan_id');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['top_up_of_loan_id']);
            $table->dropIndex(['loan_type']);
            $table->dropIndex(['top_up_of_loan_id']);

            $table->dropColumn([
                'loan_type',
                'top_up_of_loan_id',
                'top_up_amount',
                'submitted_requested_amount',
                'top_up_minimum_amount_snapshot',
                'loan_maximum_amount_snapshot',
                'declared_external_monthly_obligations',
                'declared_external_obligations_note',
                'declared_external_obligations_proof',
                'verified_net_monthly_income',
                'verified_other_monthly_income',
                'verified_external_monthly_obligations',
            ]);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_analyses', function (Blueprint $table) {
            $table->decimal('net_monthly_income', 15, 2)->nullable()->after('recommended_amount');
            $table->decimal('other_monthly_income', 15, 2)->nullable()->after('net_monthly_income');
            $table->decimal('external_monthly_obligations', 15, 2)->nullable()->after('other_monthly_income');
            $table->decimal('existing_coop_installment', 15, 2)->nullable()->after('external_monthly_obligations');
            $table->decimal('available_income', 15, 2)->nullable()->after('existing_coop_installment');
            $table->decimal('capacity_threshold_percent', 5, 2)->nullable()->after('available_income');
            $table->decimal('capacity_limit_amount', 15, 2)->nullable()->after('capacity_threshold_percent');

            $table->decimal('old_outstanding_principal', 15, 2)->nullable()->after('capacity_limit_amount');
            $table->decimal('old_principal_paid', 15, 2)->nullable()->after('old_outstanding_principal');
            $table->decimal('old_principal_repayment_percent', 7, 2)->nullable()->after('old_principal_paid');
            $table->decimal('old_monthly_installment', 15, 2)->nullable()->after('old_principal_repayment_percent');
            $table->decimal('simulated_new_installment', 15, 2)->nullable()->after('old_monthly_installment');
            $table->decimal('remaining_income_after_new_installment', 15, 2)->nullable()->after('simulated_new_installment');
        });
    }

    public function down(): void
    {
        Schema::table('loan_analyses', function (Blueprint $table) {
            $table->dropColumn([
                'net_monthly_income',
                'other_monthly_income',
                'external_monthly_obligations',
                'existing_coop_installment',
                'available_income',
                'capacity_threshold_percent',
                'capacity_limit_amount',
                'old_outstanding_principal',
                'old_principal_paid',
                'old_principal_repayment_percent',
                'old_monthly_installment',
                'simulated_new_installment',
                'remaining_income_after_new_installment',
            ]);
        });
    }
};
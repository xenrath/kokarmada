<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique();
            $table->unsignedInteger('sequence_number');

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('submitted_at');

            // Loan request
            $table->decimal('requested_amount', 15, 2);

            // Loan purpose
            $table->string('purpose_category', 20);
            $table->string('business_type', 30)->nullable();
            $table->string('business_type_other', 150)->nullable();
            $table->text('purpose_description');

            // Repayment
            $table->unsignedSmallInteger('term_months');
            $table->string('repayment_type', 20);
            $table->decimal('monthly_installment', 15, 2)->nullable();

            // Employment
            $table->string('work_unit', 150);
            $table->string('position', 150);
            $table->unsignedSmallInteger('employment_duration_years');

            // Income
            $table->decimal('other_monthly_income', 15, 2)->default(0);
            $table->string('other_income_proof')->nullable();
            $table->decimal('net_monthly_income', 15, 2)->nullable();
            $table->string('salary_slip');

            // Loan calculation
            $table->decimal('interest_rate', 5, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();

            $table->string('status', 50)->default('submitted');

            $table->timestamps();

            $table->index('user_id');
            $table->index('sequence_number');
            $table->index('status');
            $table->index('purpose_category');
            $table->index('repayment_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};

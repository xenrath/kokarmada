<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('loan_id')
                ->constrained('loans')
                ->restrictOnDelete();

            $table->unsignedSmallInteger('installment_number');

            $table->date('due_date');

            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_amount', 15, 2);
            $table->decimal('penalty_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);

            $table->timestamp('paid_at')->nullable();

            $table->string('status', 20)->default('pending');

            $table->timestamps();

            $table->unique(['loan_id', 'installment_number']);

            $table->index('loan_id');
            $table->index('due_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
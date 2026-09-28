<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_disbursements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('loan_id')
                ->unique()
                ->constrained('loans')
                ->restrictOnDelete();

            $table->foreignId('treasurer_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('account_id')
                ->constrained('accounts')
                ->restrictOnDelete();

            $table->decimal('amount', 15, 2);

            $table->timestamp('disbursed_at');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('treasurer_id');
            $table->index('account_id');
            $table->index('disbursed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_disbursements');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();

            $table->foreignId('account_id')
                ->constrained('accounts')
                ->restrictOnDelete();

            $table->foreignId('loan_id')
                ->nullable()
                ->constrained('loans')
                ->restrictOnDelete();

            $table->string('type', 20);
            $table->string('category', 50);

            $table->decimal('amount', 15, 2);

            $table->text('description')->nullable();

            $table->timestamp('occurred_at');

            $table->timestamps();

            $table->index('account_id');
            $table->index('loan_id');
            $table->index('type');
            $table->index('category');
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
    }
};

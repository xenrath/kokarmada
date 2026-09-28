<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('saving_id')
                ->constrained('savings')
                ->restrictOnDelete();

            $table->foreignId('account_id')
                ->nullable()
                ->constrained('accounts')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('type', 20);

            $table->decimal('amount', 15, 2);

            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);

            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('description')->nullable();

            $table->timestamp('occurred_at');

            $table->timestamps();

            $table->index('saving_id');
            $table->index('account_id');
            $table->index('user_id');
            $table->index('type');
            $table->index(['reference_type', 'reference_id']);
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_transactions');
    }
};

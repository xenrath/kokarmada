<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_treasurer_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('loan_id')
                ->constrained('loans')
                ->restrictOnDelete();

            $table->foreignId('treasurer_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->decimal('recommended_amount', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamp('reviewed_at');

            $table->timestamps();

            $table->index('loan_id');
            $table->index('treasurer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_treasurer_reviews');
    }
};

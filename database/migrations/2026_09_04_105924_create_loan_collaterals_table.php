<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_collaterals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('loan_id')
                ->constrained('loans')
                ->restrictOnDelete();

            $table->string('type', 50);

            $table->text('description')->nullable();

            $table->string('ownership_status', 30);

            $table->string('ownership_proof')->nullable();

            $table->string('proof_file');

            $table->timestamps();

            $table->index('loan_id');
            $table->index('type');
            $table->index('ownership_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_collaterals');
    }
};

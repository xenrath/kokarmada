<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('loan_id')
                ->constrained('loans')
                ->restrictOnDelete();

            $table->string('document_type', 50);

            $table->string('document_stage', 20);

            $table->string('file_path');

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('uploaded_at');

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('verified_at')->nullable();

            $table->string('status', 20)->default('pending');

            $table->timestamps();

            $table->index('loan_id');
            $table->index('document_type');
            $table->index('document_stage');
            $table->index('uploaded_by');
            $table->index('verified_by');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_documents');
    }
};

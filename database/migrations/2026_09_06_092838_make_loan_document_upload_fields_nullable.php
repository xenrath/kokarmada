<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('uploaded_by')->nullable()->change();
            $table->timestamp('uploaded_at')->nullable()->change();
            $table->unsignedBigInteger('verified_by')->nullable()->change();
            $table->timestamp('verified_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('loan_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('uploaded_by')->nullable(false)->change();
            $table->timestamp('uploaded_at')->nullable(false)->change();
            $table->unsignedBigInteger('verified_by')->nullable(false)->change();
            $table->timestamp('verified_at')->nullable(false)->change();
        });
    }
};

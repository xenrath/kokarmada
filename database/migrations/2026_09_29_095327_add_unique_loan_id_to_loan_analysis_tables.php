<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_analyses', function (Blueprint $table) {
            $table->dropForeign(['loan_id']);
            $table->dropIndex(['loan_id']);

            $table->unique('loan_id');

            $table->foreign('loan_id')
                ->references('id')
                ->on('loans')
                ->restrictOnDelete();
        });

        Schema::table('loan_treasurer_reviews', function (Blueprint $table) {
            $table->dropForeign(['loan_id']);
            $table->dropIndex(['loan_id']);

            $table->unique('loan_id');

            $table->foreign('loan_id')
                ->references('id')
                ->on('loans')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('loan_analyses', function (Blueprint $table) {
            $table->dropForeign(['loan_id']);
            $table->dropUnique(['loan_id']);

            $table->index('loan_id');

            $table->foreign('loan_id')
                ->references('id')
                ->on('loans')
                ->restrictOnDelete();
        });

        Schema::table('loan_treasurer_reviews', function (Blueprint $table) {
            $table->dropForeign(['loan_id']);
            $table->dropUnique(['loan_id']);

            $table->index('loan_id');

            $table->foreign('loan_id')
                ->references('id')
                ->on('loans')
                ->restrictOnDelete();
        });
    }
};

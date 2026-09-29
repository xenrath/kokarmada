<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_collaterals', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('proof_file');
            $table->foreignId('verified_by')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->restrictOnDelete();
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('verification_notes')->nullable()->after('verified_at');

            $table->index('status');
            $table->index('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('loan_collaterals', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropIndex(['status']);
            $table->dropIndex(['verified_by']);
            $table->dropColumn([
                'status',
                'verified_by',
                'verified_at',
                'verification_notes',
            ]);
        });
    }
};

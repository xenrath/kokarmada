<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shu', function (Blueprint $table) {
            $table->id();

            $table->unsignedSmallInteger('year')->unique();

            $table->decimal('total_shu', 15, 2)->default(0);

            $table->decimal('member_share_percentage', 5, 2)->default(0);
            $table->decimal('management_share_percentage', 5, 2)->default(0);
            $table->decimal('education_share_percentage', 5, 2)->default(0);
            $table->decimal('social_share_percentage', 5, 2)->default(0);
            $table->decimal('reserve_share_percentage', 5, 2)->default(0);

            $table->string('status', 20)->default('draft');

            $table->timestamp('calculated_at')->nullable();
            $table->foreignId('calculated_by')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('calculated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shu');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shu_distributions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shu_id')
                ->constrained('shu')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->decimal('member_share_amount', 15, 2)->default(0);
            $table->decimal('capital_service_amount', 15, 2)->default(0);
            $table->decimal('business_service_amount', 15, 2)->default(0);

            $table->decimal('total_amount', 15, 2);

            $table->string('status', 20)->default('calculated');

            $table->timestamp('distributed_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['shu_id', 'user_id']);

            $table->index('shu_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shu_distributions');
    }
};

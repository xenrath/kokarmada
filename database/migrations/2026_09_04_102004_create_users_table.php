<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);
            $table->string('nickname', 50)->nullable();

            $table->string('phone', 20)->unique();

            $table->string('gender', 1);

            $table->string('password');

            $table->string('role', 30)->default('member');
            $table->string('status', 20)->default('active');

            $table->rememberToken();
            $table->timestamps();

            $table->index('role');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

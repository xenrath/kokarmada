<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('member_number', 30)->unique();

            $table->string('national_id', 30)->nullable()->unique();
            $table->date('national_id_expiry')->nullable();

            $table->string('national_id_file')->nullable();
            $table->string('family_card_file')->nullable();
            $table->string('photo_file')->nullable();

            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();

            $table->text('address')->nullable();
            $table->string('postal_code', 10)->nullable();

            $table->string('occupation', 100)->nullable();

            $table->string('tax_id', 30)->nullable();

            $table->string('mother_name', 150)->nullable();

            $table->string('marital_status', 30)->nullable();

            $table->string('spouse_name', 150)->nullable();
            $table->string('spouse_occupation', 100)->nullable();

            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_profiles');
    }
};

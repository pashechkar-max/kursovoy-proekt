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
            $table->string('name', 100);
            $table->string('surname', 100);
            $table->string('email', 255)->unique();
            $table->string('phone', 30)->nullable();
            $table->date('birth_date')->nullable();

            $table->string('passport_series', 10)->nullable();
            $table->string('passport_number', 20)->nullable();
            $table->date('passport_issued_date')->nullable();
            $table->string('passport_issued_by', 255)->nullable();
            $table->string('citizenship', 100)->nullable();

            $table->unsignedBigInteger('miles')->default(0);
            $table->enum('role', ['admin', 'user'])->default('user');

            $table->string('password', 255);
            $table->rememberToken();
            $table->timestamps();

            $table->unique(['passport_series', 'passport_number'], 'users_passport_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

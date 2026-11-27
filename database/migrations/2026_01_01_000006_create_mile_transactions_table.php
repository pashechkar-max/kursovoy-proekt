<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('mile_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');

            $table->enum('type', ['welcome', 'flight', 'upgrade', 'extension']);
            $table->bigInteger('amount');
            $table->string('description', 255);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mile_transactions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id')->constrained('flights')->onDelete('cascade');
            $table->string('seat_number', 10);
            $table->enum('seat_class', ['economy', 'comfort', 'business']);
            $table->timestamps();

            $table->unique(['flight_id', 'seat_number'], 'seats_flight_seat_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};

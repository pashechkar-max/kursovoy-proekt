<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number', 20);
            $table->string('departure_city', 100);
            $table->string('arrival_city', 100);
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();

            $table->unique(['flight_number', 'departure_time'], 'flights_number_departure_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};

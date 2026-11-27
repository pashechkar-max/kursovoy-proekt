<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('pet_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('transport_type', ['cabin', 'seat', 'hold', 'guide'])->default('cabin');
            $table->enum('pet_type', ['dog', 'cat', 'bird', 'other'])->default('dog');
            $table->string('pet_name', 100);
            $table->string('breed', 100)->nullable();
            $table->decimal('weight', 5, 2);
            $table->string('carrier_dimensions', 50)->default('44x30x26');
            $table->string('vet_passport_number', 50);
            $table->boolean('rabies_vaccinated')->default(true);
            $table->decimal('service_fee', 10, 2)->default(0.00);
            $table->enum('status', ['confirmed', 'rejected', 'completed'])->default('confirmed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_bookings');
    }
};

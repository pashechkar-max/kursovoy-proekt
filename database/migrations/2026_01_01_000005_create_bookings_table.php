<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('flight_id')->constrained('flights')->onDelete('restrict');
            $table->foreignId('seat_id')->constrained('seats')->onDelete('restrict');

            $table->string('booking_number', 30)->unique();

            $table->enum('booking_class', ['economy', 'comfort', 'business']);
            $table->decimal('price', 10, 2);

            $table->string('passenger_name', 100);
            $table->string('passenger_surname', 100);
            $table->string('passport_series', 10);
            $table->string('passport_number', 20);

            $table->enum('status', ['active', 'expired', 'completed', 'cancelled'])->default('active');

            $table->dateTime('expires_at');

            $table->unsignedTinyInteger('extension_count')->default(0);

            $table->string('receipt_path', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

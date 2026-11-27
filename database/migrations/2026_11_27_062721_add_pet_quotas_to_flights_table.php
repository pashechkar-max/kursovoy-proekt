<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->unsignedSmallInteger('pet_cabin_quota')->default(4)->after('status');
            $table->unsignedSmallInteger('pet_hold_quota')->default(3)->after('pet_cabin_quota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->dropColumn(['pet_cabin_quota', 'pet_hold_quota']);
        });
    }
};

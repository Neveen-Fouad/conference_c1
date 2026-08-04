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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained();

            $table->enum('type', ['restaurant', 'hotel', 'flight']);
            $table->string('provider'); // e.g. rapidapi_hotels, rapidapi_flights, rapidapi_restaurants
            $table->string('external_reference_id'); // booking/offer ID from the external API

            $table->integer('number_of_days')->nullable(); // hotel stay length; not applicable to restaurant/flight
            $table->date('check_in_date')->nullable(); // hotel only
            $table->date('check_out_date')->nullable(); // hotel only
            $table->date('booking_date')->nullable(); // restaurant reservation date / flight departure date; not applicable to hotel
            $table->integer('number_of_bookings'); // travelers / seats / table size
            $table->enum('classes', ['luxury', 'standard', 'economy']);
            $table->enum('status', ['confirmed', 'pending', 'canceled'])->default('pending');

            $table->decimal('total_price', 10, 2);
            $table->char('currency', 3); // ISO currency code, e.g. USD, EGP

            $table->json('details'); // type-specific snapshot from the external API

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

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
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('type', ['restaurant', 'hotel', 'flight'])->change();
            $table->string('provider'); // e.g. rapidapi_hotels, rapidapi_flights, rapidapi_restaurants
            $table->string('external_reference_id'); // booking/offer ID from the external API

            $table->integer('number_of_days')->nullable()->change(); // hotel stay length; not applicable to restaurant/flight
            $table->date('check_in_date')->nullable(); // hotel only
            $table->date('check_out_date')->nullable(); // hotel only
            $table->date('booking_date')->nullable(); // restaurant reservation date / flight departure date; not applicable to hotel
            $table->enum('classes', ['luxury', 'standard', 'economy'])->nullable(); // flight only; not applicable to hotel/restaurant
            $table->enum('status', ['confirmed', 'pending', 'canceled'])->default('pending')->change();

            $table->decimal('total_price', 10, 2);
            $table->char('currency', 3); // ISO currency code, e.g. USD, EGP

            $table->json('details'); // type-specific snapshot from the external API

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

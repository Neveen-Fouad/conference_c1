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
            $table->timestamps();
            $table->foreignId("client_id")->constrained();
            $table->enum('type',["resturant","hotel","flight"]);
            $table->integer("number_of_days");
            $table->integer("number_of_bookings");
            $table->enum('classses',["luxury","standard","economy"]);
            $table->enum('status',["confirmed","pending","canceled"]);


            $table->string("booking_type");
            $table->string("provider_name");



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

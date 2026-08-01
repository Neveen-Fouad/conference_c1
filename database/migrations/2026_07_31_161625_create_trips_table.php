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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId("user_id")->constrained();
            $table->string("classes");
            $table->string("destination");
            $table->int("number_of_travels");
            $table->decimal("estimated_expenses");
            //$table->string("transport_tips");
            $table->decimal("budget");
            $table->string("attractions");
            $table->int("days");
            $table->date("start_date");

            //$table->string("style");






        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};

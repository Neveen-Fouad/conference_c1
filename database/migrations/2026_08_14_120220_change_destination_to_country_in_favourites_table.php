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
        Schema::table('favourites', function (Blueprint $table) {
            $table->enum('type', ['destination', 'country', 'hotel', 'restaurant', 'flight', 'trip'])->change();
        });

        \Illuminate\Support\Facades\DB::table('favourites')
            ->where('type', 'destination')
            ->update(['type' => 'country']);

        Schema::table('favourites', function (Blueprint $table) {
            $table->enum('type', ['country', 'hotel', 'restaurant', 'flight', 'trip'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favourites', function (Blueprint $table) {
            $table->enum('type', ['destination', 'country', 'hotel', 'restaurant', 'flight', 'trip'])->change();
        });

        \Illuminate\Support\Facades\DB::table('favourites')
            ->where('type', 'country')
            ->update(['type' => 'destination']);

        Schema::table('favourites', function (Blueprint $table) {
            $table->enum('type', ['destination', 'hotel', 'restaurant', 'flight', 'trip'])->change();
        });
    }
};

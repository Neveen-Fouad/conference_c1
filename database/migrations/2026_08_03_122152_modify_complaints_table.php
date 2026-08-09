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
            $table->enum('type', ['restaurant', 'hotel', 'flight','trip','destination'])->change();
        Schema::table('favourites', function (Blueprint $table) {
            $table->enum('type', ['restaurant', 'hotel', 'flight','trip','destination'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favourites', function (Blueprint $table) {
        Schema::table('favourites', function (Blueprint $table) {
            //
        });
    }
};

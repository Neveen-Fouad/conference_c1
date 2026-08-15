<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TYPES = ['restaurant', 'hotel', 'flight', 'trip'];

    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            Schema::table('favourites', function (Blueprint $table) {
                $table->enum('type', [...self::TYPES, 'destination'])->change();
            });
        }

        DB::table('favourites')
            ->where('type', 'destination')
            ->update(['type' => 'trip']);

        Schema::table('favourites', function (Blueprint $table) {
            $table->enum('type', self::TYPES)->change();
        });
    }

    public function down(): void
    {
        Schema::table('favourites', function (Blueprint $table) {
            $table->enum('type', [...self::TYPES, 'destination'])->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('type', ['restaurant', 'hotel', 'flight', 'trip'])->change();
            $table->dropColumn(['booking_type', 'provider_name', 'booked_at']);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_type')->nullable()->after('type');
            $table->string('provider_name')->nullable()->after('provider');
            $table->timestamp('booked_at')->nullable()->after('updated_at');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('type', ['restaurant', 'hotel', 'flight'])->change();
        });
    }
};

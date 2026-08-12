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
    Schema::table('reviews', function (Blueprint $table) {
        $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('cascade');
        $table->unique('booking_id'); // one review per booking
    });
}

public function down(): void
{
    Schema::table('reviews', function (Blueprint $table) {
        $table->dropForeign(['booking_id']);
        $table->dropUnique(['booking_id']);
        $table->dropColumn('booking_id');
    });
}
};

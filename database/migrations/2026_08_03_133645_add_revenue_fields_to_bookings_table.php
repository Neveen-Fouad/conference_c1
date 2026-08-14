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
            $table->decimal('total_price', 12, 2)->nullable()->default(0.00);
            $table->decimal('commisssion_rate', 5, 2)->nullable()->default(0.00);
            $table->decimal('commisssion_amount', 12, 2)->nullable()->default(0.00);
            // $table->string("currency")->nullable()->default("USD");
            $table->timestamp('booked_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'total_price',
                'commisssion_rate',
                'commisssion_amount',
                'booked_at',
            ]);
        });
    }
};

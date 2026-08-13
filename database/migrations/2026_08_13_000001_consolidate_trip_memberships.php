<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('trip_participants')) {
            return;
        }

        DB::table('trip_participants')
            ->orderBy('id')
            ->each(function (object $participant): void {
                DB::table('client_has_trips')->updateOrInsert(
                    [
                        'client_id' => $participant->client_id,
                        'trips_id' => $participant->trip_id,
                    ],
                    [
                        'created_at' => $participant->created_at ?? now(),
                        'updated_at' => $participant->updated_at ?? now(),
                    ]
                );
            });

        Schema::drop('trip_participants');
    }

    public function down(): void
    {
        Schema::create('trip_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['trip_id', 'client_id']);
        });
    }
};

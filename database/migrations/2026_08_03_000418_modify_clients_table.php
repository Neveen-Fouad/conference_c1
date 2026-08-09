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
        Schema::table('clients', function (Blueprint $table) {
        $table->string('long');
        $table->string('latittude');
        $table->date('birth_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
        $table->dropColumn('long');
        $table->dropColumn('latittude');
        $table->dateTime('birth_date')->change();
        });
    }
};

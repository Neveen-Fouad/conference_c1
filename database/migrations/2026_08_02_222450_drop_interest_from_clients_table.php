<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('clients', 'interest')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('interest');
            });
        }
    }

    public function down(): void
    {
        //
    }
};
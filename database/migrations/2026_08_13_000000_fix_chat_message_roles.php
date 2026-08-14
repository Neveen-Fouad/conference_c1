<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->enum('role', ['user', 'assistant'])->change();
        });
    }

    public function down(): void
    {
        // The previous constraint was invalid, so it must not be restored.
    }
};

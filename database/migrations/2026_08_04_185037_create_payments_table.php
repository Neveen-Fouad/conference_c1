<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained()->restrictOnDelete();

            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();

            $table->string('payment_reference')->unique();
            $table->string('payment_type')->default('booking');

            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('EGP');

            $table->string('gateway')->default('paymob');

            $table->enum('status', ['pending', 'paid', 'failed', 'canceled', 'refunded',])->default('pending');

            $table->string('gateway_reference')->nullable()->unique();

            $table->string('gateway_transaction_id')->nullable()->unique();
            $table->string('payment_method')->nullable();
            $table->text('failure_reason')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

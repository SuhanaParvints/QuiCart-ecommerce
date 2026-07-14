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

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('gateway')->default('razorpay');

            $table->string('razorpay_order_id')->unique();
            $table->string('razorpay_payment_id')->unique();
            $table->string('razorpay_signature');

            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('INR');

            $table->enum('status', [
                'pending',
                'paid',
                'failed',
                'refunded',
            ])->default('paid');

            $table->json('order_ids')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
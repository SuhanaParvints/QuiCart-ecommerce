<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->text('address');
            $table->string('location');
            $table->string('state');
            $table->string('pincode');
            $table->string('status')->default('Pending');
            $table->string('payment_status')->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
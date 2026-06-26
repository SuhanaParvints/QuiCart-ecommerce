<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('category');
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            $table->decimal('price', 10, 2);
            $table->decimal('rating', 2, 1)->default(5.0);

            $table->integer('stock')->default(0);

            $table->enum('status', [
                'in_stock',
                'limited_stock',
                'sold_out'
            ])->default('in_stock');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
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
        Schema::create('intents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 64)->unique(); // örn. product_recommend, cart_add, order_checkout
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('threshold', 5, 2)->default(0.75); // benzerlik eşiği
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intents');
    }
};

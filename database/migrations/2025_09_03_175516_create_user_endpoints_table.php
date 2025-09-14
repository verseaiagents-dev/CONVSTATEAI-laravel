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
        Schema::create('user_endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('intent_type'); // order-tracking, cargo-tracking, add-to-cart, product-detail
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('method'); // GET, POST
            $table->string('endpoint_url');
            $table->json('headers')->nullable(); // Custom headers
            $table->json('payload_template')->nullable(); // Request payload template
            $table->boolean('is_active')->default(true);
            $table->integer('timeout')->default(30); // seconds
            $table->timestamps();
            
            // Unique constraint: one active endpoint per intent per user
            $table->unique(['user_id', 'intent_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_endpoints');
    }
};

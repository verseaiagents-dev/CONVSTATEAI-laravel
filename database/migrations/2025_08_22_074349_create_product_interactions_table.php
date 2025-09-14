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
        Schema::create('product_interactions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->string('action'); // view, compare, buy, add_to_cart
            $table->timestamp('timestamp');
            $table->string('source')->default('chat_widget'); // chat_widget, product_page, dashboard
            $table->json('metadata')->nullable(); // Additional data like product_url, user_agent, etc.
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['session_id', 'action']);
            $table->index(['product_id', 'action']);
            $table->index(['timestamp', 'action']);
            $table->index(['source', 'action']);
            
            // Foreign key constraint
            $table->foreign('session_id')->references('session_id')->on('enhanced_chat_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_interactions');
    }
};

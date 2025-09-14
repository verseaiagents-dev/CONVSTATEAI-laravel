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
        Schema::create('enhanced_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->json('intent_history')->nullable();
            $table->json('chat_history')->nullable();
            $table->integer('daily_view_count')->default(0);
            $table->integer('daily_view_limit')->default(20);
            $table->timestamp('last_activity')->nullable();
            $table->json('user_preferences')->nullable();
            $table->json('product_interactions')->nullable();
            $table->string('status')->default('active'); // active, expired, completed
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['last_activity', 'status']);
            $table->index(['daily_view_count', 'daily_view_limit']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enhanced_chat_sessions');
    }
};

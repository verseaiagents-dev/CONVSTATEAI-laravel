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
        Schema::dropIfExists('usage_tokens');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('usage_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->integer('tokens_remaining')->default(0);
            $table->integer('tokens_used')->default(0);
            $table->integer('tokens_total')->default(0);
            $table->date('reset_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index(['subscription_id', 'is_active']);
        });
    }
};

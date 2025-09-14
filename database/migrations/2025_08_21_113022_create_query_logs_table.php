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
        Schema::create('query_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id')->default(1);
            $table->string('session_id', 255)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('query_text');
            $table->string('detected_intent', 255)->nullable();
            $table->decimal('confidence_score', 3, 2)->nullable();
            $table->text('response_text')->nullable();
            $table->string('response_template', 255)->nullable();
            $table->json('chunks_used')->nullable(); // Kullanılan chunk ID'leri
            $table->integer('response_time_ms')->nullable();
            $table->boolean('is_successful')->default(true);
            $table->text('error_message')->nullable();
            $table->enum('user_feedback', ['helpful', 'not_helpful', 'neutral'])->nullable();
            $table->timestamps();
            
            $table->index(['site_id', 'session_id']);
            $table->index('user_id');
            $table->index('detected_intent');
            $table->index('confidence_score');
            $table->index('user_feedback');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('query_logs');
    }
};

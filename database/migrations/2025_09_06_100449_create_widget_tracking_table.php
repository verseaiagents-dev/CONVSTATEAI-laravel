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
        Schema::create('widget_tracking', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->string('project_id')->nullable();
            $table->string('event_type'); // 'product_link_click', 'widget_interaction', etc.
            $table->string('intent')->nullable(); // 'recommendation', 'search', etc.
            $table->string('product_name')->nullable();
            $table->text('product_url')->nullable();
            $table->json('metadata')->nullable(); // Additional tracking data
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index(['session_id', 'event_type']);
            $table->index(['project_id', 'created_at']);
            $table->index('intent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_tracking');
    }
};

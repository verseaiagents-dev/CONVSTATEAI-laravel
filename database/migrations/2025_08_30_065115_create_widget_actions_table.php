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
        Schema::create('widget_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('widget_customization_id');
            $table->string('action_name', 100); // Action identifier
            $table->string('display_name', 100); // User-friendly name
            $table->text('description')->nullable(); // Action description
            $table->string('endpoint_url')->nullable(); // API endpoint
            $table->enum('http_method', ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'])->default('GET');
            $table->json('request_headers')->nullable(); // Custom headers
            $table->json('request_body')->nullable(); // Request body template
            $table->json('response_mapping')->nullable(); // Response field mapping
            $table->boolean('requires_authentication')->default(false);
            $table->string('authentication_type', 50)->nullable(); // Bearer, Basic, etc.
            $table->json('authentication_config')->nullable(); // Auth configuration
            $table->integer('timeout_seconds')->default(30);
            $table->boolean('is_active')->default(true);
            $table->integer('execution_order')->default(0); // Action execution order
            $table->json('conditions')->nullable(); // When to execute this action
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['widget_customization_id', 'is_active']);
            $table->index('action_name');
            $table->index('execution_order');
            
            // Foreign key constraint
            $table->foreign('widget_customization_id')->references('id')->on('widget_customizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_actions');
    }
};

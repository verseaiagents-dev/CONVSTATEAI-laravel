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
        Schema::create('knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id')->default(1);
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->enum('source_type', ['file', 'url', 'api', 'manual']);
            $table->string('source_path', 1000)->nullable();
            $table->json('source_config')->nullable(); // API keys, headers, etc.
            $table->enum('file_type', ['json', 'xml', 'csv', 'excel', 'pdf', 'txt'])->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->integer('total_records')->default(0);
            $table->integer('processed_records')->default(0);
            $table->integer('chunk_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_processing')->default(false);
            $table->timestamp('last_processed_at')->nullable();
            $table->enum('processing_status', ['pending', 'processing', 'completed', 'failed', 'mapped'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index('site_id');
            $table->index('source_type');
            $table->index('processing_status');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_bases');
    }
};

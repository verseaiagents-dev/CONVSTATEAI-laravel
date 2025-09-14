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
        Schema::create('knowledge_chunks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('knowledge_base_id');
            $table->integer('chunk_index');
            $table->text('content');
            $table->string('content_hash', 64);
            $table->enum('content_type', ['product', 'faq', 'blog', 'review', 'category', 'general']);
            $table->string('entity_type', 100)->nullable(); // products, categories, brands, etc.
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('metadata')->nullable(); // {table_name, field_names, record_id, etc.}
            $table->text('image_vision')->nullable();
            $table->boolean('has_images')->default(false);
            $table->integer('processed_images')->default(0);
            $table->json('embedding_vector')->nullable(); // AI embedding vector
            $table->string('vector_id', 255)->nullable(); // External vector DB ID
            $table->integer('chunk_size'); // karakter sayısı
            $table->integer('word_count');
            $table->boolean('is_indexed')->default(false);
            $table->timestamps();
            
            $table->index('knowledge_base_id');
            $table->index('chunk_index');
            $table->index('content_type');
            $table->index(['entity_type', 'entity_id']);
            $table->index('is_indexed');
            // $table->fullText('content'); // Removed for SQLite compatibility
            
            $table->foreign('knowledge_base_id')->references('id')->on('knowledge_bases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_chunks');
    }
};

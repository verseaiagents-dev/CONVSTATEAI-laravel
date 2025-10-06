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
        Schema::create('prompt_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->enum('category', [
                'intent_detection',
                'response_generation', 
                'campaign_generation',
                'faq_optimization',
                'content_analysis',
                'order_mapping',
                'image_analysis',
                'product_recommendation',
                'category_browse',
                'price_inquiry',
                'general_help',
                'custom'
            ])->index();
            $table->text('content');
            $table->json('variables')->nullable(); // Dinamik değişkenler
            $table->json('metadata')->nullable(); // Ek bilgiler
            $table->boolean('is_active')->default(true)->index();
            $table->enum('environment', ['test', 'production'])->default('production')->index();
            $table->integer('version')->default(1);
            $table->integer('priority')->default(0); // Öncelik sırası
            $table->string('language', 5)->default('tr');
            $table->text('description')->nullable();
            $table->json('tags')->nullable(); // Etiketler
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index(['category', 'is_active', 'environment']);
            $table->index(['environment', 'priority']);
            $table->index('last_used_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompt_templates');
    }
};

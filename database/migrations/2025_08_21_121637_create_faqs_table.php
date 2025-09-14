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
        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->string('category');
                $table->text('answer');
                $table->string('short_answer');
                $table->boolean('is_active')->default(true);
                $table->foreignId('site_id')->constrained()->onDelete('cascade');
                $table->integer('sort_order')->default(0);
                $table->json('tags')->nullable();
                $table->integer('view_count')->default(0);
                $table->integer('helpful_count')->default(0);
                $table->integer('not_helpful_count')->default(0);
                $table->timestamps();

                $table->index(['site_id', 'is_active']);
                $table->index(['category', 'is_active']);
                $table->index(['sort_order', 'id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};

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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->string('discount');
            $table->datetime('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed', 'buy_x_get_y', 'free_shipping'])->default('buy_x_get_y');
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('minimum_order_amount', 10, 2)->nullable();
            $table->integer('max_usage')->nullable();
            $table->integer('current_usage')->default(0);
            $table->string('image_url')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->boolean('ai_generated')->default(false);
            $table->decimal('ai_confidence_score', 5, 2)->nullable();
            $table->timestamps();

            $table->index(['site_id', 'is_active']);
            $table->index(['category', 'is_active']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};

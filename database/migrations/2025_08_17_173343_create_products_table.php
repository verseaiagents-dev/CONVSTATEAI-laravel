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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id')->default(1);
            $table->string('external_id')->nullable();
            $table->string('name', 500);
            $table->text('description')->nullable();
            $table->string('short_description', 1000)->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('barcode', 100)->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('subcategory_id')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->dateTime('sale_start_date')->nullable();
            $table->dateTime('sale_end_date')->nullable();
            $table->decimal('weight', 8, 3)->nullable();
            $table->json('dimensions')->nullable(); // {length, width, height, unit}
            $table->integer('stock_quantity')->default(0);
            $table->integer('stock')->default(0);
            $table->decimal('profit_margin', 5, 2)->default(20.00);
            $table->integer('low_stock_threshold')->default(5);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_bestseller')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_on_sale')->default(false);
            $table->decimal('rating_average', 3, 2)->default(0.00);
            $table->integer('rating_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('sold_count')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('seo_url', 500)->nullable();
            $table->json('tags')->nullable();
            $table->json('attributes')->nullable(); // {color, size, material, etc.}
            $table->json('variants')->nullable(); // {color_variants, size_variants}
            $table->json('images')->nullable(); // {main_image, gallery_images, thumbnail}
            $table->json('documents')->nullable(); // {manual, warranty, datasheet}
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['site_id', 'category_id']);
            $table->index('brand_id');
            $table->index('price');
            $table->index('stock_quantity');
            $table->index('rating_average');
            $table->index(['is_on_sale', 'sale_end_date']);
            // $table->fullText(['name', 'description', 'tags']); // Removed for SQLite compatibility
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

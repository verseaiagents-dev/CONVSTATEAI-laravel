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
        Schema::create('user_woo_commerce_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('store_url');
            $table->string('consumer_key');
            $table->text('consumer_secret');
            $table->boolean('verify_ssl')->default(false);
            $table->string('api_version')->default('v3');
            $table->boolean('wp_api_integration')->default(true);
            $table->boolean('query_string_auth')->default(false);
            $table->integer('timeout')->default(15);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index for better performance
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_woo_commerce_configs');
    }
};

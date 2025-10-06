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
        Schema::create('system_costs', function (Blueprint $table) {
            $table->id();
            $table->string('intent_type'); // Intent kategorisi
            $table->string('operation_type'); // GPT, Knowledge Base, Search, etc.
            $table->integer('base_tokens'); // Temel token sayısı
            $table->decimal('base_cost_usd', 10, 6); // Temel maliyet USD
            $table->decimal('base_cost_tl', 10, 2); // Temel maliyet TL
            $table->json('cost_multipliers'); // Çarpanlar (complexity, length, etc.)
            $table->json('additional_operations'); // Ek işlemler (KB search, etc.)
            $table->text('description'); // Açıklama
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['intent_type', 'operation_type']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_costs');
    }
};

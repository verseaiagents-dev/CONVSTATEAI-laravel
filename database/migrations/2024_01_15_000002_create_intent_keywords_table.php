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
        Schema::create('intent_keywords', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('intent_id');
            $table->string('keyword', 128); // örn. "öner", "tavsiye", "sepete ekle"
            $table->decimal('weight', 5, 2)->default(1.0);
            $table->timestamps();

            $table->foreign('intent_id')->references('id')->on('intents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intent_keywords');
    }
};

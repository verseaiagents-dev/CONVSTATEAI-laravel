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
        // Önce foreign key constraint'leri kaldır
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
        });
        
        // Mevcut plans tablosunu temizle
        Schema::dropIfExists('plans');
        
        // Yeni yapıyla plans tablosunu oluştur
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->enum('billing_cycle', ['monthly', 'yearly', 'trial']);
            $table->json('features');
            $table->boolean('is_active')->default(true);
            $table->integer('trial_days')->nullable(); // Freemium için 7 günlük deneme
            $table->timestamps();
        });
        
        // Foreign key constraint'i yeniden ekle
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
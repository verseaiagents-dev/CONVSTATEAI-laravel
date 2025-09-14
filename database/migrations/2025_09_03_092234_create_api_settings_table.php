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
        Schema::create('api_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // API adı (örn: OpenAI, Google AI, etc.)
            $table->string('provider'); // API sağlayıcısı (openai, google, anthropic, etc.)
            $table->text('api_key'); // Şifrelenmiş API key
            $table->string('base_url')->nullable(); // API base URL'i
            $table->json('config')->nullable(); // Ek konfigürasyon ayarları
            $table->boolean('is_active')->default(true); // Aktif/pasif durumu
            $table->boolean('is_default')->default(false); // Varsayılan API
            $table->text('description')->nullable(); // Açıklama
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_settings');
    }
};

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
        Schema::create('widget_customizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ai_name', 100)->default('Convstate AI');
            $table->string('welcome_message', 500)->default('Merhaba ben Kadir, senin dijital asistanınım. Sana nasıl yardımcı olabilirim?');
            $table->string('primary_color', 7)->default('#007bff'); // Hex color code
            $table->string('secondary_color', 7)->default('#6c757d');
            $table->string('font_family', 50)->default('Roboto, sans-serif');
            $table->integer('font_size')->default(14);
            $table->boolean('show_avatar')->default(true);
            $table->string('avatar_url')->nullable();
            $table->json('customization_data')->nullable();
            $table->json('chat_settings')->nullable(); // Chat behavior settings
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'is_active']);
            $table->index('last_used_at');
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_customizations');
    }
};

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
        Schema::create('notification_widget_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id');
            $table->string('message_text')->default('Sizin için kampanyamız var.');
            $table->boolean('is_active')->default(true);
            $table->string('color_theme')->default('purple'); // purple, blue, green, orange
            $table->integer('display_duration')->default(5000); // milliseconds
            $table->string('animation_type')->default('fade-in'); // fade-in, slide-in, bounce
            $table->boolean('show_close_button')->default(true);
            $table->string('redirect_url')->nullable(); // Optional redirect when clicked
            $table->timestamps();
            
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            $table->index(['site_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_widget_settings');
    }
};

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
        Schema::table('widget_customizations', function (Blueprint $table) {
            // Sadece eksik olan kolonları ekle
            if (!Schema::hasColumn('widget_customizations', 'language')) {
                $table->string('language', 5)->default('tr')->after('secondary_color');
            }
            
            if (!Schema::hasColumn('widget_customizations', 'custom_messages')) {
                $table->json('custom_messages')->nullable()->after('language');
            }
            
            if (!Schema::hasColumn('widget_customizations', 'rate_limit_per_minute')) {
                $table->integer('rate_limit_per_minute')->default(10)->after('custom_messages');
            }
            
            if (!Schema::hasColumn('widget_customizations', 'api_timeout_seconds')) {
                $table->integer('api_timeout_seconds')->default(10)->after('rate_limit_per_minute');
            }
            
            if (!Schema::hasColumn('widget_customizations', 'max_retry_attempts')) {
                $table->integer('max_retry_attempts')->default(2)->after('api_timeout_seconds');
            }
            
            if (!Schema::hasColumn('widget_customizations', 'enable_typing_indicator')) {
                $table->boolean('enable_typing_indicator')->default(true)->after('max_retry_attempts');
            }
            
            if (!Schema::hasColumn('widget_customizations', 'enable_sound_notifications')) {
                $table->boolean('enable_sound_notifications')->default(false)->after('enable_typing_indicator');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('widget_customizations', function (Blueprint $table) {
            $table->dropColumn([
                'ai_personality',
                'welcome_message_custom',
                'cargo_not_found_message',
                'feature_disabled_message',
                'error_message_template',
                'order_not_found_message',
                'theme_color',
                'logo_url',
                'font_family',
                'primary_color',
                'secondary_color',
                'language',
                'custom_messages',
                'rate_limit_per_minute',
                'api_timeout_seconds',
                'max_retry_attempts',
                'enable_typing_indicator',
                'enable_sound_notifications'
            ]);
        });
    }
};

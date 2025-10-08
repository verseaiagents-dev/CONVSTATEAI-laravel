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
            // Add missing columns that are in the model's fillable array
            if (!Schema::hasColumn('widget_customizations', 'ai_personality')) {
                $table->string('ai_personality', 50)->default('friendly')->after('welcome_message');
            }
            
            if (!Schema::hasColumn('widget_customizations', 'welcome_message_custom')) {
                $table->text('welcome_message_custom')->nullable()->after('ai_personality');
            }
            
            if (!Schema::hasColumn('widget_customizations', 'cargo_not_found_message')) {
                $table->text('cargo_not_found_message')->nullable()->after('welcome_message_custom');
            }
            
            if (!Schema::hasColumn('widget_customizations', 'feature_disabled_message')) {
                $table->text('feature_disabled_message')->nullable()->after('cargo_not_found_message');
            }
            
            if (!Schema::hasColumn('widget_customizations', 'error_message_template')) {
                $table->text('error_message_template')->nullable()->after('feature_disabled_message');
            }
            
            if (!Schema::hasColumn('widget_customizations', 'order_not_found_message')) {
                $table->text('order_not_found_message')->nullable()->after('error_message_template');
            }
            
            if (!Schema::hasColumn('widget_customizations', 'theme_color')) {
                $table->string('theme_color', 7)->default('#3b82f6')->after('order_not_found_message');
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
                'theme_color'
            ]);
        });
    }
};

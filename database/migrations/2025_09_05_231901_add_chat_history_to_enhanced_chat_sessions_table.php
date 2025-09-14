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
        Schema::table('enhanced_chat_sessions', function (Blueprint $table) {
            // Performance için index'ler (eğer yoksa)
            if (!Schema::hasIndex('enhanced_chat_sessions', 'enhanced_chat_sessions_created_at_index')) {
                $table->index('created_at');
            }
            if (!Schema::hasIndex('enhanced_chat_sessions', 'enhanced_chat_sessions_last_activity_index')) {
                $table->index('last_activity');
            }
            if (!Schema::hasIndex('enhanced_chat_sessions', 'enhanced_chat_sessions_status_index')) {
                $table->index('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enhanced_chat_sessions', function (Blueprint $table) {
            // Index'leri kaldır
            $table->dropIndex(['created_at']);
            $table->dropIndex(['last_activity']);
            $table->dropIndex(['status']);
        });
    }
};

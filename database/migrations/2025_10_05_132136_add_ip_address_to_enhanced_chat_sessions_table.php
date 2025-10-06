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
            $table->string('ip_address')->nullable()->after('session_id');
            $table->string('user_agent')->nullable()->after('ip_address');
            $table->index(['ip_address', 'status']);
            $table->index(['ip_address', 'daily_view_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enhanced_chat_sessions', function (Blueprint $table) {
            $table->dropIndex(['ip_address', 'status']);
            $table->dropIndex(['ip_address', 'daily_view_count']);
            $table->dropColumn(['ip_address', 'user_agent']);
        });
    }
};
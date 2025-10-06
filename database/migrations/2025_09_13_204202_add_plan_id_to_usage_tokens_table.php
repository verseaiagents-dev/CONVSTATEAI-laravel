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
        // Check if usage_tokens table exists before trying to modify it
        if (Schema::hasTable('usage_tokens')) {
            Schema::table('usage_tokens', function (Blueprint $table) {
                $table->unsignedBigInteger('plan_id')->nullable()->after('subscription_id');
                $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usage_tokens', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');
        });
    }
};

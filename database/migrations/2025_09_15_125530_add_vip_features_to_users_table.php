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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('usage_token')->default(0)->after('tokens_remaining');
            $table->integer('max_projects')->default(0)->after('usage_token');
            $table->boolean('priority_support')->default(false)->after('max_projects');
            $table->boolean('advanced_analytics')->default(false)->after('priority_support');
            $table->boolean('custom_branding')->default(false)->after('advanced_analytics');
            $table->boolean('api_access')->default(false)->after('custom_branding');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'usage_token',
                'max_projects',
                'priority_support',
                'advanced_analytics',
                'custom_branding',
                'api_access'
            ]);
        });
    }
};

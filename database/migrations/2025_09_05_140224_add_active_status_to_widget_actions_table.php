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
        Schema::table('widget_actions', function (Blueprint $table) {
            if (!Schema::hasColumn('widget_actions', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('endpoint');
            }
            if (!Schema::hasColumn('widget_actions', 'display_name')) {
                $table->string('display_name')->nullable()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('widget_actions', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'display_name']);
        });
    }
};
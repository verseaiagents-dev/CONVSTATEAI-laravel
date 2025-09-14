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
            // Drop the existing unique constraint on user_id
            $table->dropUnique(['user_id']);
            
            // Add new unique constraint on user_id and project_id combination
            $table->unique(['user_id', 'project_id'], 'widget_customizations_user_project_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('widget_customizations', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('widget_customizations_user_project_unique');
            
            // Restore the original unique constraint on user_id only
            $table->unique('user_id');
        });
    }
};
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
            // Add project_id if it doesn't exist
            if (!Schema::hasColumn('widget_customizations', 'project_id')) {
                $table->unsignedBigInteger('project_id')->after('user_id');
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            }

            // Add customization_token if it doesn't exist
            if (!Schema::hasColumn('widget_customizations', 'customization_token')) {
                $table->string('customization_token')->unique()->after('project_id');
            }

            // Add a unique constraint for user_id and project_id combination
            $table->unique(['user_id', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('widget_customizations', function (Blueprint $table) {
            // Remove the unique constraint for user_id and project_id
            $table->dropUnique(['user_id', 'project_id']);

            // Add back the unique constraint to user_id
            $table->unique('user_id');

            // Drop the foreign key for project_id
            $table->dropForeign(['project_id']);

            // Drop the columns
            $table->dropColumn(['project_id', 'customization_token']);
        });
    }
};

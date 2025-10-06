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
            $table->string('widget_position')->default('right')->after('theme_color');
            $table->dropColumn('logo_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('widget_customizations', function (Blueprint $table) {
            $table->dropColumn('widget_position');
            $table->string('logo_url')->nullable();
        });
    }
};

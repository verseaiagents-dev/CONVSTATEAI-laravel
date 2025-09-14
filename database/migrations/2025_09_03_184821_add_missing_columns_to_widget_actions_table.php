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
            $table->string('siparis_durumu_endpoint')->nullable()->after('widget_customization_id');
            $table->string('kargo_durumu_endpoint')->nullable()->after('siparis_durumu_endpoint');
            $table->string('http_action')->default('GET')->after('kargo_durumu_endpoint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('widget_actions', function (Blueprint $table) {
            $table->dropColumn(['siparis_durumu_endpoint', 'kargo_durumu_endpoint', 'http_action']);
        });
    }
};

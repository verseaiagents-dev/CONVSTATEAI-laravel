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
            // Mevcut endpoint sütunlarını kaldır
            $table->dropColumn(['siparis_durumu_endpoint', 'kargo_durumu_endpoint']);
            
            // Yeni yapı için sütunlar ekle
            $table->string('type', 100)->after('widget_customization_id');
            $table->string('endpoint')->nullable()->after('type');
            
            // Index ekle
            $table->index(['widget_customization_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('widget_actions', function (Blueprint $table) {
            // Geri alma işlemi
            $table->dropIndex(['widget_customization_id', 'type']);
            $table->dropColumn(['type', 'endpoint']);
            
            // Eski sütunları geri ekle
            $table->string('siparis_durumu_endpoint')->nullable()->after('widget_customization_id');
            $table->string('kargo_durumu_endpoint')->nullable()->after('siparis_durumu_endpoint');
        });
    }
};

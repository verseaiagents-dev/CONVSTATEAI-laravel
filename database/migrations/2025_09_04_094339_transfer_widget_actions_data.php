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
        // Veri transferi zaten yapıldı, sadece log yazdır
        echo "Veri transferi tamamlandı. Yeni yapıya geçildi.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri alma işlemi - yeni verileri eski yapıya çevir
        $newActions = DB::table('widget_actions')->whereIn('type', ['siparis_durumu_endpoint', 'kargo_durumu_endpoint'])->get();
        
        $groupedActions = $newActions->groupBy('widget_customization_id');
        
        foreach ($groupedActions as $widgetId => $actions) {
            $siparisEndpoint = $actions->where('type', 'siparis_durumu_endpoint')->first();
            $kargoEndpoint = $actions->where('type', 'kargo_durumu_endpoint')->first();
            
            DB::table('widget_actions')->insert([
                'widget_customization_id' => $widgetId,
                'siparis_durumu_endpoint' => $siparisEndpoint->endpoint ?? null,
                'kargo_durumu_endpoint' => $kargoEndpoint->endpoint ?? null,
                'http_action' => $siparisEndpoint->http_action ?? $kargoEndpoint->http_action ?? 'GET',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // Yeni verileri sil
        DB::table('widget_actions')->whereIn('type', ['siparis_durumu_endpoint', 'kargo_durumu_endpoint'])->delete();
    }
};

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
        // Sütunlar varsa transfer işlemini yap
        if (!Schema::hasColumn('widget_actions', 'siparis_durumu_endpoint') && !Schema::hasColumn('widget_actions', 'kargo_durumu_endpoint')) {
            return; // Sütunlar yoksa işlemi atla
        }
        
        // Mevcut verileri yeni yapıya transfer et
        $existingActions = DB::table('widget_actions')->get();
        
        foreach ($existingActions as $action) {
            $widgetCustomizationId = $action->widget_customization_id;
            
            // Sipariş durumu endpoint'i varsa ekle
            if (!empty($action->siparis_durumu_endpoint)) {
                DB::table('widget_actions')->insert([
                    'widget_customization_id' => $widgetCustomizationId,
                    'type' => 'siparis_durumu_endpoint',
                    'endpoint' => $action->siparis_durumu_endpoint,
                    'http_action' => $action->http_action ?? 'GET',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Kargo durumu endpoint'i varsa ekle
            if (!empty($action->kargo_durumu_endpoint)) {
                DB::table('widget_actions')->insert([
                    'widget_customization_id' => $widgetCustomizationId,
                    'type' => 'kargo_durumu_endpoint',
                    'endpoint' => $action->kargo_durumu_endpoint,
                    'http_action' => $action->http_action ?? 'GET',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        // Eski verileri sil (sütunlar varsa)
        if (Schema::hasColumn('widget_actions', 'siparis_durumu_endpoint') || Schema::hasColumn('widget_actions', 'kargo_durumu_endpoint')) {
            DB::table('widget_actions')->whereNotNull('siparis_durumu_endpoint')->orWhereNotNull('kargo_durumu_endpoint')->delete();
        }
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

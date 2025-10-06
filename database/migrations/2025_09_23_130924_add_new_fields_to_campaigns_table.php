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
        Schema::table('campaigns', function (Blueprint $table) {
            // Yeni tasarım modeli fieldları
            $table->string('campaign_code')->nullable()->unique()->after('title'); // Kampanya kodu
            $table->text('target_audience')->nullable()->after('description'); // Hedef kitle
            $table->json('product_ids')->nullable()->after('target_audience'); // Kampanyaya dahil ürünler
            $table->decimal('budget_limit', 10, 2)->nullable()->after('minimum_order_amount'); // Bütçe limiti
            $table->integer('priority_level')->default(1)->after('budget_limit'); // Öncelik seviyesi (1-5)
            $table->boolean('requires_approval')->default(false)->after('priority_level'); // Onay gerektiriyor mu
            $table->string('approval_status')->default('pending')->after('requires_approval'); // Onay durumu
            $table->text('notes')->nullable()->after('approval_status'); // Notlar
            $table->json('metadata')->nullable()->after('notes'); // Ek metadata
            $table->timestamp('last_modified_at')->nullable()->after('metadata'); // Son değişiklik tarihi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'campaign_code',
                'target_audience',
                'product_ids',
                'budget_limit',
                'priority_level',
                'requires_approval',
                'approval_status',
                'notes',
                'metadata',
                'last_modified_at'
            ]);
        });
    }
};

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
        Schema::create('field_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_base_id')->constrained('knowledge_bases')->onDelete('cascade');
            $table->string('source_field'); // Kullanıcının dosyasındaki field adı
            $table->string('target_field'); // Sistemimizdeki standart field adı
            $table->string('field_type')->default('text'); // text, number, date, boolean, array
            $table->boolean('is_required')->default(false); // Zorunlu mu?
            $table->text('default_value')->nullable(); // Varsayılan değer
            $table->json('transformation')->nullable(); // Dönüşüm kuralları
            $table->json('validation_rules')->nullable(); // Validation kuralları
            $table->integer('mapping_order')->default(0); // Mapping sırası
            $table->boolean('is_active')->default(true); // Aktif mi?
            $table->timestamps();
            
            // Indexes
            $table->index(['knowledge_base_id', 'source_field']);
            $table->index(['knowledge_base_id', 'target_field']);
            $table->index('mapping_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_mappings');
    }
};

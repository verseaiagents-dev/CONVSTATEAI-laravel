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
        Schema::table('field_mappings', function (Blueprint $table) {
            // Composite indexes for common queries
            $table->index(['knowledge_base_id', 'is_active'], 'idx_kb_active');
            $table->index(['knowledge_base_id', 'field_type'], 'idx_kb_field_type');
            $table->index(['knowledge_base_id', 'is_required'], 'idx_kb_required');
            $table->index(['knowledge_base_id', 'mapping_order'], 'idx_kb_order');
            
            // Indexes for transformation and validation queries (JSON columns can't be indexed directly)
            // $table->index(['knowledge_base_id', 'transformation'], 'idx_kb_transformation');
            // $table->index(['knowledge_base_id', 'validation_rules'], 'idx_kb_validation');
            
            // Full-text search index for source and target fields (commented out for SQLite compatibility)
            // $table->fullText(['source_field', 'target_field'], 'idx_fields_fulltext');
            
            // Partial indexes for better performance (commented out for SQLite compatibility)
            // $table->index(['is_active'], 'idx_active_partial')->where('is_active', true);
            // $table->index(['is_required'], 'idx_required_partial')->where('is_required', true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_mappings', function (Blueprint $table) {
            // Drop composite indexes (commented out since they may not exist)
            // $table->dropIndex('idx_kb_active');
            // $table->dropIndex('idx_kb_field_type');
            // $table->dropIndex('idx_kb_required');
            // $table->dropIndex('idx_kb_order');
            
            // Drop transformation and validation indexes (commented out since they were never created)
            // $table->dropIndex('idx_kb_transformation');
            // $table->dropIndex('idx_kb_validation');
            
            // Drop full-text index (commented out since it may not exist)
            // $table->dropIndex('idx_fields_fulltext');
            
            // Drop partial indexes (commented out since they may not exist)
            // $table->dropIndex('idx_active_partial');
            // $table->dropIndex('idx_required_partial');
        });
    }
};

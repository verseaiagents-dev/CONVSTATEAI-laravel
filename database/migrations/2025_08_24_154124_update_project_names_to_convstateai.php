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
        // Update products table - descriptions, metadata
        if (Schema::hasTable('products')) {
            DB::table('products')->where('description', 'like', '%ConvAI%')
                ->orWhere('description', 'like', '%TestAI%')
                ->orWhere('description', 'like', '%Conv%')
                ->update(['description' => DB::raw("REPLACE(REPLACE(REPLACE(description, 'ConvAI', 'ConvStateAI'), 'TestAI', 'ConvStateAI'), 'Conv', 'ConvStateAI')")]);
        }
        
        // Update categories table - descriptions
        if (Schema::hasTable('categories')) {
            DB::table('categories')->where('description', 'like', '%ConvAI%')
                ->orWhere('description', 'like', '%TestAI%')
                ->orWhere('description', 'like', '%Conv%')
                ->update(['description' => DB::raw("REPLACE(REPLACE(REPLACE(description, 'ConvAI', 'ConvStateAI'), 'TestAI', 'ConvStateAI'), 'Conv', 'ConvStateAI')")]);
        }
        
        // Update brands table - descriptions
        if (Schema::hasTable('brands')) {
            DB::table('brands')->where('description', 'like', '%ConvAI%')
                ->orWhere('description', 'like', '%TestAI%')
                ->orWhere('description', 'like', '%Conv%')
                ->update(['description' => DB::raw("REPLACE(REPLACE(REPLACE(description, 'ConvAI', 'ConvStateAI'), 'TestAI', 'ConvStateAI'), 'Conv', 'ConvStateAI')")]);
        }
        
        // Update knowledge_base table - content
        if (Schema::hasTable('knowledge_base')) {
            DB::table('knowledge_base')->where('content', 'like', '%ConvAI%')
                ->orWhere('content', 'like', '%TestAI%')
                ->orWhere('description', 'like', '%ConvAI%')
                ->orWhere('description', 'like', '%TestAI%')
                ->update([
                    'content' => DB::raw("REPLACE(REPLACE(REPLACE(content, 'ConvAI', 'ConvStateAI'), 'TestAI', 'ConvStateAI'), 'Conv', 'ConvStateAI')"),
                    'description' => DB::raw("REPLACE(REPLACE(REPLACE(description, 'ConvAI', 'ConvStateAI'), 'TestAI', 'ConvStateAI'), 'Conv', 'ConvStateAI')")
                ]);
        }
        

        
        // Update campaigns table - descriptions
        if (Schema::hasTable('campaigns')) {
            DB::table('campaigns')->where('description', 'like', '%ConvAI%')
                ->orWhere('description', 'like', '%TestAI%')
                ->orWhere('description', 'like', '%Conv%')
                ->update(['description' => DB::raw("REPLACE(REPLACE(REPLACE(description, 'ConvAI', 'ConvStateAI'), 'TestAI', 'ConvStateAI'), 'Conv', 'ConvStateAI')")]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration updates content, so we can't easily reverse it
        // The down method is intentionally left empty
    }
};

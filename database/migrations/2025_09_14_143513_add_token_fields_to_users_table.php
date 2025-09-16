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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('tokens_total')->default(0)->after('phone');
            $table->integer('tokens_used')->default(0)->after('tokens_total');
            $table->integer('tokens_remaining')->default(0)->after('tokens_used');
            $table->date('token_reset_date')->nullable()->after('tokens_remaining');
            $table->unsignedBigInteger('current_plan_id')->nullable()->after('token_reset_date');
            $table->foreign('current_plan_id')->references('id')->on('plans')->onDelete('set null');
            
            $table->index(['current_plan_id']);
            $table->index(['token_reset_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_plan_id']);
            $table->dropIndex(['current_plan_id']);
            $table->dropIndex(['token_reset_date']);
            $table->dropColumn([
                'tokens_total',
                'tokens_used', 
                'tokens_remaining',
                'token_reset_date',
                'current_plan_id'
            ]);
        });
    }
};

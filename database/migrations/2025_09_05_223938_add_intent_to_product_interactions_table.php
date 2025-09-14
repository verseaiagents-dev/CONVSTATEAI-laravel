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
        Schema::table('product_interactions', function (Blueprint $table) {
            $table->string('intent')->nullable()->after('session_id');
            $table->text('user_message')->nullable()->after('intent');
            $table->boolean('success')->default(true)->after('user_message');
            $table->decimal('response_time', 8, 3)->nullable()->after('success');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_interactions', function (Blueprint $table) {
            $table->dropColumn(['intent', 'user_message', 'success', 'response_time']);
        });
    }
};

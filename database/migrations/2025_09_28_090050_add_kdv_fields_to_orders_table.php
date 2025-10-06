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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('amount_without_kdv', 10, 2)->nullable()->after('amount');
            $table->decimal('kdv_amount', 10, 2)->nullable()->after('amount_without_kdv');
            $table->decimal('kdv_rate', 5, 4)->nullable()->after('kdv_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['amount_without_kdv', 'kdv_amount', 'kdv_rate']);
        });
    }
};

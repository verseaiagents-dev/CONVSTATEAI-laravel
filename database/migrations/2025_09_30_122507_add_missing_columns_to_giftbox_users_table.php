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
        Schema::table('giftbox_users', function (Blueprint $table) {
            if (!Schema::hasColumn('giftbox_users', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('giftbox_users', 'surname')) {
                $table->string('surname');
            }
            if (!Schema::hasColumn('giftbox_users', 'mail')) {
                $table->string('mail');
            }
            if (!Schema::hasColumn('giftbox_users', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('giftbox_users', 'visitors')) {
                $table->string('visitors')->nullable();
            }
            if (!Schema::hasColumn('giftbox_users', 'sector')) {
                $table->string('sector');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('giftbox_users', function (Blueprint $table) {
            $table->dropColumn(['name', 'surname', 'mail', 'phone', 'visitors', 'sector']);
        });
    }
};
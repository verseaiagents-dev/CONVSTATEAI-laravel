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
        // Update existing users who have personal_token but no token_expires_at
        \App\Models\User::whereNotNull('personal_token')
            ->whereNull('token_expires_at')
            ->update(['token_expires_at' => now()->addYears(10)]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this migration
    }
};

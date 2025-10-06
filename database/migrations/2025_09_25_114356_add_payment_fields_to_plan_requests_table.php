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
        Schema::table('plan_requests', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->nullable()->after('status');
            $table->string('company_name')->nullable()->after('amount');
            $table->string('full_name')->nullable()->after('company_name');
            $table->string('email')->nullable()->after('full_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('tax_number')->nullable()->after('phone');
            $table->string('tax_office')->nullable()->after('tax_number');
            $table->string('country')->nullable()->after('tax_office');
            $table->string('city')->nullable()->after('country');
            $table->string('district')->nullable()->after('city');
            $table->text('address_line')->nullable()->after('district');
            $table->string('postal_code')->nullable()->after('address_line');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_requests', function (Blueprint $table) {
            $table->dropColumn([
                'amount',
                'company_name',
                'full_name',
                'email',
                'phone',
                'tax_number',
                'tax_office',
                'country',
                'city',
                'district',
                'address_line',
                'postal_code'
            ]);
        });
    }
};
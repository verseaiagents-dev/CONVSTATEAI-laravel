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
        Schema::create('event_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('event_template_id');
            $table->enum('action_type', ['http_call', 'db_insert', 'log', 'notify']); // http: {url, method, headers, body}, db: {table, data}
            $table->json('config'); // http: {url, method, headers, body}, db: {table, data}
            $table->integer('seq')->default(1); // sıralı çalıştırma
            $table->timestamps();

            $table->foreign('event_template_id')->references('id')->on('event_templates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_actions');
    }
};

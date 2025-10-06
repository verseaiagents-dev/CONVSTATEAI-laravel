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
        Schema::table('faqs', function (Blueprint $table) {
            // Yeni tasarım modeli fieldları
            $table->string('faq_code')->nullable()->unique()->after('title'); // SSS kodu
            $table->text('keywords')->nullable()->after('description'); // Anahtar kelimeler
            $table->json('related_faqs')->nullable()->after('keywords'); // İlgili SSS'ler
            $table->string('difficulty_level')->default('easy')->after('related_faqs'); // Zorluk seviyesi
            $table->integer('estimated_read_time')->nullable()->after('difficulty_level'); // Tahmini okuma süresi (saniye)
            $table->boolean('featured')->default(false)->after('estimated_read_time'); // Öne çıkan SSS
            $table->string('author')->nullable()->after('featured'); // Yazar
            $table->timestamp('last_reviewed_at')->nullable()->after('author'); // Son gözden geçirme tarihi
            $table->text('review_notes')->nullable()->after('last_reviewed_at'); // Gözden geçirme notları
            $table->json('metadata')->nullable()->after('review_notes'); // Ek metadata
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropColumn([
                'faq_code',
                'keywords',
                'related_faqs',
                'difficulty_level',
                'estimated_read_time',
                'featured',
                'author',
                'last_reviewed_at',
                'review_notes',
                'metadata'
            ]);
        });
    }
};

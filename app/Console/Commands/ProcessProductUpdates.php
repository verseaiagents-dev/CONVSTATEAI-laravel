<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessProductUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-product-updates {--test-image-analysis : Resim analizi test et} {--refresh-chunks : Mevcut chunk\'larda resim analizi yenile} {--create-test-chunk : Test chunk oluştur}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ürün güncellemelerini işler ve resim analizi yapar';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('test-image-analysis')) {
            $this->testImageAnalysis();
            return;
        }
        
        if ($this->option('refresh-chunks')) {
            $this->refreshExistingChunks();
            return;
        }
        
        if ($this->option('create-test-chunk')) {
            $this->createTestChunk();
            return;
        }
        
        $this->info('Ürün güncellemeleri işleniyor...');
        // Ana işlem mantığı buraya eklenebilir
    }

    /**
     * Resim analizi test komutu
     */
    public function testImageAnalysis()
    {
        $this->info('Resim analizi test ediliyor...');
        
        try {
            $aiService = app(\App\Services\KnowledgeBase\AIService::class);
            
            // Test için public erişilebilir resim URL'i
            $testImageUrl = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400';
            $context = 'Bu bir akıllı saat ürün resmidir.';
            
            $this->info("Test resim URL: {$testImageUrl}");
            $this->info("Context: {$context}");
            
            // Resim analizi yap
            $result = $aiService->analyzeImageContent($testImageUrl, $context);
            
            $this->info('Resim analizi sonucu:');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Product Type', $result['product_type'] ?? 'N/A'],
                    ['Category', $result['category'] ?? 'N/A'],
                    ['Visual Features', implode(', ', $result['visual_features'] ?? [])],
                    ['Technical Features', implode(', ', $result['technical_features'] ?? [])],
                    ['Usage Area', $result['usage_area'] ?? 'N/A'],
                    ['Target Audience', $result['target_audience'] ?? 'N/A'],
                    ['Summary', $result['summary'] ?? 'N/A']
                ]
            );
            
            // JSON formatında da göster
            $this->info('JSON formatında:');
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Chunk içeriğinde resim analizi test et
            $this->info("\n" . str_repeat('=', 50));
            $this->info('Chunk içeriğinde resim analizi test ediliyor...');
            
            $chunkContent = "Bu akıllı saat, modern teknoloji ile donatılmıştır. 
            <img src=\"{$testImageUrl}\" alt=\"Akıllı Saat\" />
            Ürün özellikleri arasında GPS, nabız ölçer ve Bluetooth bulunmaktadır.";
            
            $chunkResult = $aiService->processChunkImages($chunkContent, $context);
            
            $this->info('Chunk resim analizi sonucu:');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Has Images', $chunkResult['has_images'] ? 'Yes' : 'No'],
                    ['Processed Images', $chunkResult['processed_images']],
                    ['Image URLs', implode(', ', $chunkResult['image_urls'] ?? [])],
                    ['Image Vision', $chunkResult['image_vision'] ? 'Available' : 'None']
                ]
            );
            
        } catch (\Exception $e) {
            $this->error('Resim analizi hatası: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Test için örnek chunk oluşturur ve resim analizi yapar
     */
    public function createTestChunk()
    {
        $this->info('Test chunk oluşturuluyor...');
        
        try {
            // Test içeriği oluştur
            $testContent = "Bu bir test ürün chunk'ıdır. Bu chunk'ta resim analizi yapılacak ve image_vision field'ı doldurulacak. Ürün özellikleri arasında GPS, nabız ölçer ve Bluetooth bulunmaktadır. Ayrıca bu ürün günlük kullanım için idealdir ve modern teknoloji ile donatılmıştır. <img src=\"https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400\" alt=\"Akıllı Saat\" /> Bu resimde görülen ürün hakkında detaylı bilgi verilecek.";
            
            $this->info("Test içeriği uzunluğu: " . strlen($testContent) . " karakter");
            
            // ContentChunker kullanarak chunk oluştur
            $contentChunker = app(\App\Services\KnowledgeBase\ContentChunker::class);
            
            // Chunking konfigürasyonu
            $config = [
                'max_chunk_size' => 100, // Daha küçük chunk boyutu
                'overlap_size' => 20,
                'min_chunk_size' => 50
            ];
            
            $this->info("Chunking konfigürasyonu: " . json_encode($config));
            
            $chunks = $contentChunker->chunkContent($testContent, $config);
            
            $this->info("Chunks array tipi: " . gettype($chunks));
            $this->info("Chunks array boyutu: " . (is_array($chunks) ? count($chunks) : 'Array değil'));
            
            if (is_array($chunks) && !empty($chunks)) {
                $this->info("İlk chunk örneği: " . json_encode($chunks[0], JSON_PRETTY_PRINT));
            }
            
            $this->info("Toplam " . count($chunks) . " test chunk oluşturuldu.");
            
            // Her chunk'ı veritabanına kaydet
            $knowledgeBase = \App\Models\KnowledgeBase::find(2);
            foreach ($chunks as $chunk) {
                \App\Models\KnowledgeChunk::create([
                    'knowledge_base_id' => 2, // Mevcut knowledge base ID
                    'project_id' => $knowledgeBase->project_id ?? null, // Proje ID'sini ekle
                    'chunk_index' => $chunk['chunk_index'],
                    'content' => $chunk['content'],
                    'content_hash' => $chunk['content_hash'],
                    'chunk_size' => $chunk['chunk_size'],
                    'word_count' => $chunk['word_count'],
                    'content_type' => $chunk['content_type'],
                    'metadata' => $chunk['metadata'],
                    'image_vision' => $chunk['image_vision'] ?? null,
                    'has_images' => $chunk['has_images'] ?? false,
                    'processed_images' => $chunk['processed_images'] ?? 0
                ]);
                
                $this->info("Chunk {$chunk['chunk_index']} kaydedildi - Resim: " . ($chunk['has_images'] ? 'Var' : 'Yok'));
            }
            
            $this->info('Test chunk\'lar başarıyla oluşturuldu!');
            
        } catch (\Exception $e) {
            $this->error('Test chunk oluşturma hatası: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Mevcut chunk'larda resim analizi yeniler
     */
    public function refreshExistingChunks()
    {
        $this->info('Mevcut chunk\'larda resim analizi yenileniyor...');
        
        try {
            $aiService = app(\App\Services\KnowledgeBase\AIService::class);
            
            // KnowledgeChunk modelini kullanarak mevcut chunk'ları al
            $chunks = \App\Models\KnowledgeChunk::all();
            
            if ($chunks->isEmpty()) {
                $this->warn('Hiç chunk bulunamadı.');
                return;
            }
            
            $this->info("Toplam {$chunks->count()} chunk bulundu. Resim analizi yapılıyor...");
            
            $progressBar = $this->output->createProgressBar($chunks->count());
            $progressBar->start();
            
            $updatedCount = 0;
            $errorCount = 0;
            
            foreach ($chunks as $chunk) {
                try {
                    // Chunk içeriğinde resim var mı kontrol et
                    $imageAnalysis = $aiService->processChunkImages(
                        $chunk->content, 
                        $chunk->metadata['context'] ?? ''
                    );
                    
                    // Chunk'ı güncelle
                    $chunk->image_vision = $imageAnalysis['image_vision'];
                    $chunk->has_images = $imageAnalysis['has_images'];
                    $chunk->processed_images = $imageAnalysis['processed_images'];
                    
                    // Metadata'yı güncelle
                    $metadata = $chunk->metadata ?? [];
                    $metadata['image_analysis'] = [
                        'has_images' => $imageAnalysis['has_images'],
                        'processed_images' => $imageAnalysis['processed_images'],
                        'image_urls' => $imageAnalysis['image_urls'] ?? [],
                        'last_updated' => now()->toISOString()
                    ];
                    $chunk->metadata = $metadata;
                    
                    $chunk->save();
                    $updatedCount++;
                    
                    if ($imageAnalysis['has_images']) {
                        $this->line("\nChunk {$chunk->id} güncellendi - {$imageAnalysis['processed_images']} resim işlendi");
                    }
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->error("\nChunk {$chunk->id} hatası: " . $e->getMessage());
                    
                    // Hata durumunda chunk'ı olduğu gibi bırak
                    $chunk->image_vision = null;
                    $chunk->has_images = false;
                    $chunk->processed_images = 0;
                    $chunk->save();
                }
                
                $progressBar->advance();
                
                // Rate limiting için kısa bekleme
                usleep(500000); // 0.5 saniye
            }
            
            $progressBar->finish();
            
            $this->newLine(2);
            $this->info('Resim analizi yenileme tamamlandı!');
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Toplam Chunk', $chunks->count()],
                    ['Güncellenen', $updatedCount],
                    ['Hatalı', $errorCount],
                    ['Başarı Oranı', round(($updatedCount / $chunks->count()) * 100, 2) . '%']
                ]
            );
            
            // Özet istatistikler
            $this->showChunkSummary();
            
        } catch (\Exception $e) {
            $this->error('Chunk yenileme hatası: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Chunk özet istatistiklerini gösterir
     */
    private function showChunkSummary()
    {
        try {
            $chunks = \App\Models\KnowledgeChunk::all();
            
            $summary = [
                'total_chunks' => $chunks->count(),
                'chunks_with_images' => $chunks->where('has_images', true)->count(),
                'chunks_without_images' => $chunks->where('has_images', false)->count(),
                'total_images_processed' => $chunks->sum('processed_images'),
                'chunks_with_vision' => $chunks->whereNotNull('image_vision')->count()
            ];
            
            $this->info('Chunk Özet İstatistikleri:');
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Toplam Chunk', $summary['total_chunks']],
                    ['Resimli Chunk', $summary['chunks_with_images']],
                    ['Resimsiz Chunk', $summary['chunks_without_images']],
                    ['İşlenen Toplam Resim', $summary['total_images_processed']],
                    ['Vision Analizi Olan', $summary['chunks_with_vision']]
                ]
            );
            
        } catch (\Exception $e) {
            $this->warn('Özet istatistikler gösterilemedi: ' . $e->getMessage());
        }
    }
}

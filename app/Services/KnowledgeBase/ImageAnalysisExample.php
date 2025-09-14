<?php

namespace App\Services\KnowledgeBase;

use Illuminate\Support\Facades\Log;

/**
 * Knowledge Base Resim Analizi Örnek Kullanımı
 * 
 * Bu sınıf, Knowledge Base chunk'larında resim analizi yapmanın
 * nasıl kullanılacağını gösterir.
 */
class ImageAnalysisExample
{
    private $aiService;
    private $contentChunker;

    public function __construct(AIService $aiService, ContentChunker $contentChunker)
    {
        $this->aiService = $aiService;
        $this->contentChunker = $contentChunker;
    }

    /**
     * Ürün içeriğini chunk'lara böler ve resim analizi yapar
     */
    public function processProductContent(string $content, array $config = []): array
    {
        try {
            // Content'i chunk'lara böl
            $chunks = $this->contentChunker->chunkContent($content, $config);
            
            Log::info('Content chunked successfully', [
                'total_chunks' => count($chunks),
                'content_length' => strlen($content)
            ]);

            return $chunks;
            
        } catch (\Exception $e) {
            Log::error('Content chunking error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mevcut chunk'larda resim analizi yapar
     */
    public function analyzeExistingChunks(array $chunks): array
    {
        $analyzedChunks = [];
        
        foreach ($chunks as $chunk) {
            try {
                // Chunk'ta resim var mı kontrol et
                $imageAnalysis = $this->aiService->processChunkImages(
                    $chunk['content'], 
                    $chunk['metadata']['context'] ?? ''
                );
                
                // Chunk'ı güncelle
                $chunk['image_vision'] = $imageAnalysis['image_vision'];
                $chunk['has_images'] = $imageAnalysis['has_images'];
                $chunk['processed_images'] = $imageAnalysis['processed_images'];
                
                if ($imageAnalysis['has_images']) {
                    Log::debug('Image analysis completed for chunk', [
                        'chunk_index' => $chunk['chunk_index'],
                        'image_urls' => $imageAnalysis['image_urls'],
                        'image_vision' => $imageAnalysis['image_vision']
                    ]);
                }
                
                $analyzedChunks[] = $chunk;
                
            } catch (\Exception $e) {
                Log::error('Chunk image analysis error: ' . $e->getMessage(), [
                    'chunk_index' => $chunk['chunk_index'] ?? 'unknown'
                ]);
                
                // Hata durumunda chunk'ı olduğu gibi ekle
                $chunk['image_vision'] = null;
                $chunk['has_images'] = false;
                $chunk['processed_images'] = 0;
                $analyzedChunks[] = $chunk;
            }
        }
        
        return $analyzedChunks;
    }

    /**
     * Ürün resimlerini toplu olarak analiz eder
     */
    public function batchAnalyzeProductImages(array $imageUrls, string $context = ''): array
    {
        $results = [];
        
        foreach ($imageUrls as $index => $imageUrl) {
            try {
                $this->info("Resim analiz ediliyor: " . ($index + 1) . "/" . count($imageUrls));
                
                $analysis = $this->aiService->analyzeImageContent($imageUrl, $context);
                
                $results[] = [
                    'image_url' => $imageUrl,
                    'analysis' => $analysis,
                    'status' => 'success'
                ];
                
                // Rate limiting için kısa bekleme
                if ($index < count($imageUrls) - 1) {
                    sleep(1);
                }
                
            } catch (\Exception $e) {
                Log::error('Batch image analysis error: ' . $e->getMessage(), [
                    'image_url' => $imageUrl,
                    'index' => $index
                ]);
                
                $results[] = [
                    'image_url' => $imageUrl,
                    'analysis' => null,
                    'status' => 'error',
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }

    /**
     * Chunk'lardan resim URL'lerini çıkarır
     */
    public function extractImageUrlsFromChunks(array $chunks): array
    {
        $allImageUrls = [];
        
        foreach ($chunks as $chunk) {
            $imageAnalysis = $this->aiService->processChunkImages($chunk['content']);
            
            if ($imageAnalysis['has_images']) {
                $allImageUrls = array_merge($allImageUrls, $imageAnalysis['image_urls']);
            }
        }
        
        return array_unique($allImageUrls);
    }

    /**
     * Resim analizi sonuçlarını özetler
     */
    public function summarizeImageAnalysis(array $chunks): array
    {
        $summary = [
            'total_chunks' => count($chunks),
            'chunks_with_images' => 0,
            'total_images_processed' => 0,
            'image_categories' => [],
            'product_types' => []
        ];
        
        foreach ($chunks as $chunk) {
            if ($chunk['has_images'] ?? false) {
                $summary['chunks_with_images']++;
                $summary['total_images_processed'] += $chunk['processed_images'] ?? 0;
                
                // image_vision'dan kategori ve ürün türü bilgilerini çıkar
                if ($chunk['image_vision']) {
                    $visionData = json_decode($chunk['image_vision'], true);
                    
                    if ($visionData) {
                        if (isset($visionData['category'])) {
                            $summary['image_categories'][] = $visionData['category'];
                        }
                        
                        if (isset($visionData['product_type'])) {
                            $summary['product_types'][] = $visionData['product_type'];
                        }
                    }
                }
            }
        }
        
        // Kategorileri say
        $summary['image_categories'] = array_count_values($summary['image_categories']);
        $summary['product_types'] = array_count_values($summary['product_types']);
        
        return $summary;
    }

    /**
     * Test için örnek kullanım
     */
    public function runExample(): void
    {
        $this->info('Knowledge Base Resim Analizi Örneği');
        $this->info('=====================================');
        
        // Örnek ürün içeriği
        $sampleContent = "
        Bu akıllı saat, modern teknoloji ile donatılmıştır.
        
        <img src=\"https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400\" alt=\"Akıllı Saat\" />
        
        Ürün özellikleri arasında GPS, nabız ölçer ve Bluetooth bulunmaktadır.
        
        Ayrıca bu kulaklık da yüksek kaliteli ses sunar:
        
        <img src=\"https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400\" alt=\"Kulaklık\" />
        
        Bu ürünler günlük kullanım için idealdir.
        ";
        
        $this->info('Örnek içerik işleniyor...');
        
        try {
            // Content'i chunk'lara böl
            $chunks = $this->processProductContent($sampleContent, [
                'max_chunk_size' => 500,
                'overlap_size' => 100
            ]);
            
            $this->info("Toplam {$chunks->count()} chunk oluşturuldu.");
            
            // Chunk'larda resim analizi yap
            $analyzedChunks = $this->analyzeExistingChunks($chunks);
            
            // Özet çıkar
            $summary = $this->summarizeImageAnalysis($analyzedChunks);
            
            $this->info('Analiz özeti:');
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Toplam Chunk', $summary['total_chunks']],
                    ['Resimli Chunk', $summary['chunks_with_images']],
                    ['İşlenen Resim', $summary['total_images_processed']],
                    ['Kategoriler', json_encode($summary['image_categories'])],
                    ['Ürün Türleri', json_encode($summary['product_types'])]
                ]
            );
            
        } catch (\Exception $e) {
            $this->error('Örnek çalıştırma hatası: ' . $e->getMessage());
        }
    }

    private function info(string $message): void
    {
        echo "[INFO] {$message}\n";
    }

    private function error(string $message): void
    {
        echo "[ERROR] {$message}\n";
    }

    private function table(array $headers, array $rows): void
    {
        // Basit tablo çıktısı
        echo "\n";
        foreach ($headers as $header) {
            echo str_pad($header, 20) . " | ";
        }
        echo "\n" . str_repeat('-', 60) . "\n";
        
        foreach ($rows as $row) {
            foreach ($row as $cell) {
                echo str_pad($cell, 20) . " | ";
            }
            echo "\n";
        }
        echo "\n";
    }
}

<?php

namespace App\Jobs;

use App\Models\KnowledgeBase;
use App\Models\KnowledgeChunk;
use App\Services\ContentChunkerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProcessKnowledgeBaseFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $knowledgeBase;
    protected $filePath;
    protected $fileType;
    protected $contentChunker;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public $timeout = 300; // 5 dakika

    /**
     * Create a new job instance.
     */
    public function __construct(KnowledgeBase $knowledgeBase, string $filePath, string $fileType)
    {
        $this->knowledgeBase = $knowledgeBase;
        $this->filePath = $filePath;
        $this->fileType = $fileType;
        $this->contentChunker = app(\App\Services\KnowledgeBase\ContentChunker::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Processing knowledge base file', [
                'knowledge_base_id' => $this->knowledgeBase->id,
                'file_path' => $this->filePath,
                'file_type' => $this->fileType
            ]);

            // Mark as processing
            $this->knowledgeBase->markAsProcessing();

            // Delete existing chunks for this knowledge base
            KnowledgeChunk::where('knowledge_base_id', $this->knowledgeBase->id)->delete();
            Log::info('Deleted existing chunks for knowledge base', [
                'knowledge_base_id' => $this->knowledgeBase->id
            ]);

            // Get file content
            $fileContent = Storage::disk('public')->get($this->filePath);
            if (!$fileContent) {
                throw new \Exception('File not found: ' . $this->filePath);
            }

            // Create temporary file for processing
            $tempFile = tempnam(sys_get_temp_dir(), 'kb_process_');
            file_put_contents($tempFile, $fileContent);

            // Create file object for processing
            $file = new \Illuminate\Http\UploadedFile(
                $tempFile,
                basename($this->filePath),
                $this->getMimeType($this->fileType),
                null,
                true
            );

            // Process file and create chunks
            $chunks = $this->processFileAndCreateChunks($file, $this->fileType, $this->knowledgeBase);

            // Update knowledge base with results
            $this->knowledgeBase->update([
                'chunk_count' => count($chunks),
                'total_records' => $this->getTotalRecords($file, $this->fileType),
                'processed_records' => count($chunks),
                'processing_status' => 'completed',
                'is_processing' => false,
                'last_processed_at' => Carbon::now(),
            ]);

            // Clean up temp file
            unlink($tempFile);

            Log::info('Knowledge base file processing completed', [
                'knowledge_base_id' => $this->knowledgeBase->id,
                'chunk_count' => count($chunks)
            ]);

        } catch (\Exception $e) {
            Log::error('Knowledge base file processing failed', [
                'knowledge_base_id' => $this->knowledgeBase->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->knowledgeBase->markAsFailed($e->getMessage());
            
            // Re-throw to mark job as failed
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Knowledge base file processing job failed permanently', [
            'knowledge_base_id' => $this->knowledgeBase->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        $this->knowledgeBase->markAsFailed('Job failed after ' . $this->attempts() . ' attempts: ' . $exception->getMessage());
    }

    /**
     * Process file and create chunks
     */
    private function processFileAndCreateChunks($file, $extension, $knowledgeBase)
    {
        $chunks = [];
        
        try {
            // Parse file content based on type
            switch ($extension) {
                case 'csv':
                    $chunks = $this->processCsvFile($file);
                    break;
                case 'json':
                    $chunks = $this->processJsonFile($file);
                    break;
                case 'txt':
                    $chunks = $this->processTextFile($file);
                    break;
                case 'xml':
                    $chunks = $this->processXmlFile($file);
                    break;
                case 'xlsx':
                case 'xls':
                    $chunks = $this->processExcelFile($file);
                    break;
                default:
                    throw new \Exception("Unsupported file type: {$extension}");
            }

            // Create chunk models
            $chunkModels = [];
            foreach ($chunks as $index => $chunkData) {
                $contentType = $this->determineContentType($chunkData['content'], $extension);
                
                $chunkModels[] = KnowledgeChunk::create([
                    'knowledge_base_id' => $knowledgeBase->id,
                    'chunk_index' => $index,
                    'content' => $chunkData['content'],
                    'content_hash' => $chunkData['content_hash'],
                    'content_type' => $contentType,
                    'chunk_size' => $chunkData['chunk_size'],
                    'word_count' => $chunkData['word_count'],
                    'has_images' => false,
                    'processed_images' => 0,
                    'image_vision' => null,
                    'metadata' => array_merge($chunkData['metadata'] ?? [], [
                        'original_content_type' => $chunkData['content_type'] ?? 'unknown',
                        'detected_content_type' => $contentType
                    ]),
                ]);
            }

            // FAQ optimization for small files
            $totalContentSize = collect($chunks)->sum('chunk_size');
            if ($totalContentSize < 50000) {
                $this->optimizeFAQContent($knowledgeBase, $chunks);
            }

            return $chunkModels;

        } catch (\Exception $e) {
            Log::error('Error processing file chunks', [
                'knowledge_base_id' => $knowledgeBase->id,
                'extension' => $extension,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Process CSV file
     */
    private function processCsvFile($file)
    {
        $content = $this->readFileSafely($file->getPathname());
        
        // UTF-8 BOM kontrolü ve temizleme
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }
        
        $lines = str_getcsv($content, "\n");
        $chunks = [];
        
        foreach ($lines as $index => $line) {
            if (empty(trim($line))) continue;
            
            // UTF-8 güvenli karakter sayımı
            $chunkSize = mb_strlen($line, 'UTF-8');
            $wordCount = $this->countWordsUtf8($line);
            
            $chunks[] = [
                'content' => $line,
                'content_hash' => hash('sha256', $line),
                'chunk_size' => $chunkSize,
                'word_count' => $wordCount,
                'content_type' => 'csv_row',
                'metadata' => ['row_index' => $index + 1]
            ];
        }
        
        return $chunks;
    }

    /**
     * Process JSON file
     */
    private function processJsonFile($file)
    {
        $content = $this->readFileSafely($file->getPathname());
        
        // UTF-8 BOM kontrolü ve temizleme
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }
        
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON file: ' . json_last_error_msg());
        }
        
        $chunks = [];
        
        if (is_array($data)) {
            foreach ($data as $index => $item) {
                $itemContent = is_array($item) ? json_encode($item, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : (string)$item;
                
                // UTF-8 güvenli karakter sayımı
                $chunkSize = mb_strlen($itemContent, 'UTF-8');
                $wordCount = $this->countWordsUtf8($itemContent);
                
                $chunks[] = [
                    'content' => $itemContent,
                    'content_hash' => hash('sha256', $itemContent),
                    'chunk_size' => $chunkSize,
                    'word_count' => $wordCount,
                    'content_type' => 'json_item',
                    'metadata' => ['item_index' => $index]
                ];
            }
        } else {
            // UTF-8 güvenli karakter sayımı
            $chunkSize = mb_strlen($content, 'UTF-8');
            $wordCount = $this->countWordsUtf8($content);
            
            $chunks[] = [
                'content' => $content,
                'content_hash' => hash('sha256', $content),
                'chunk_size' => $chunkSize,
                'word_count' => $wordCount,
                'content_type' => 'json_document',
                'metadata' => []
            ];
        }
        
        return $chunks;
    }

    /**
     * Process text file
     */
    private function processTextFile($file)
    {
        $content = $this->readFileSafely($file->getPathname());
        
        // UTF-8 BOM kontrolü ve temizleme
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }
        
        // Split content into chunks using UTF-8 safe methods
        $chunkSize = 1000; // 1000 karakter per chunk
        $chunks = [];
        $contentLength = mb_strlen($content, 'UTF-8');
        
        for ($i = 0; $i < $contentLength; $i += $chunkSize) {
            $chunk = mb_substr($content, $i, $chunkSize, 'UTF-8');
            
            // UTF-8 güvenli karakter sayımı
            $chunkByteSize = mb_strlen($chunk, 'UTF-8');
            $wordCount = $this->countWordsUtf8($chunk);
            
            $chunks[] = [
                'content' => $chunk,
                'content_hash' => hash('sha256', $chunk),
                'chunk_size' => $chunkByteSize,
                'word_count' => $wordCount,
                'content_type' => 'text_chunk',
                'metadata' => ['chunk_start' => $i, 'chunk_end' => min($i + $chunkSize, $contentLength)]
            ];
        }
        
        return $chunks;
    }

    /**
     * Process XML file
     */
    private function processXmlFile($file)
    {
        $content = $this->readFileSafely($file->getPathname());
        
        // UTF-8 BOM kontrolü ve temizleme
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }
        
        $xml = simplexml_load_string($content);
        
        if ($xml === false) {
            throw new \Exception('Invalid XML file');
        }
        
        $chunks = [];
        $this->processXmlNode($xml, $chunks);
        
        return $chunks;
    }

    /**
     * Process XML node recursively
     */
    private function processXmlNode($node, &$chunks, $depth = 0)
    {
        $content = $node->asXML();
        
        // UTF-8 güvenli karakter sayımı
        $chunkSize = mb_strlen($content, 'UTF-8');
        $wordCount = $this->countWordsUtf8($content);
        
        $chunks[] = [
            'content' => $content,
            'content_hash' => hash('sha256', $content),
            'chunk_size' => $chunkSize,
            'word_count' => $wordCount,
            'content_type' => 'xml_node',
            'metadata' => ['node_name' => $node->getName(), 'depth' => $depth]
        ];
        
        foreach ($node->children() as $child) {
            $this->processXmlNode($child, $chunks, $depth + 1);
        }
    }

    /**
     * Process Excel file
     */
    private function processExcelFile($file)
    {
        // This would require PhpSpreadsheet package
        // For now, return empty array
        return [];
    }

    /**
     * Get total records count
     */
    private function getTotalRecords($file, $extension)
    {
        switch ($extension) {
            case 'csv':
                $content = $this->readFileSafely($file->getPathname());
                return count(str_getcsv($content, "\n"));
            case 'json':
                $content = $this->readFileSafely($file->getPathname());
                $data = json_decode($content, true);
                return is_array($data) ? count($data) : 1;
            case 'txt':
                $content = $this->readFileSafely($file->getPathname());
                return ceil(mb_strlen($content, 'UTF-8') / 1000);
            default:
                return 1;
        }
    }

    /**
     * Determine content type
     */
    private function determineContentType($content, $extension)
    {
        // FAQ detection
        if (preg_match('/\b(soru|cevap|faq|frequently|asked|questions?)\b/i', $content)) {
            return 'faq';
        }
        
        // Product detection
        if (preg_match('/\b(ürün|product|fiyat|price|stok|stock)\b/i', $content)) {
            return 'product';
        }
        
        // General content
        return 'general';
    }

    /**
     * Get MIME type for file extension
     */
    private function getMimeType($extension)
    {
        $mimeTypes = [
            'csv' => 'text/csv',
            'json' => 'application/json',
            'txt' => 'text/plain',
            'xml' => 'application/xml',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel'
        ];
        
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    /**
     * Optimize FAQ content
     */
    private function optimizeFAQContent($knowledgeBase, $chunks)
    {
        try {
            // FAQ optimization logic here
            Log::info('FAQ optimization started', [
                'knowledge_base_id' => $knowledgeBase->id,
                'chunk_count' => count($chunks)
            ]);
        } catch (\Exception $e) {
            Log::warning('FAQ optimization failed', [
                'knowledge_base_id' => $knowledgeBase->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Safely read file content with UTF-8 encoding
     */
    private function readFileSafely(string $path): string
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new \Exception("Dosya okunamadı: " . $path);
        }
        
        // UTF-8 encoding kontrolü ve düzeltme
        if (!mb_check_encoding($content, 'UTF-8')) {
            // Farklı encoding'leri dene
            $encodings = ['ISO-8859-1', 'ISO-8859-9', 'Windows-1254', 'Windows-1252', 'ASCII'];
            
            foreach ($encodings as $encoding) {
                if (mb_check_encoding($content, $encoding)) {
                    $content = mb_convert_encoding($content, 'UTF-8', $encoding);
                    break;
                }
            }
            
            // Hala UTF-8 değilse, force convert
            if (!mb_check_encoding($content, 'UTF-8')) {
                $content = mb_convert_encoding($content, 'UTF-8', 'auto');
                Log::warning("File encoding force converted to UTF-8: " . $path);
            }
        }
        
        return $content;
    }

    /**
     * Count words in UTF-8 text safely
     */
    private function countWordsUtf8(string $text): int
    {
        // Boşluk ve noktalama işaretlerini temizle
        $text = preg_replace('/[\s\p{P}]+/u', ' ', $text);
        $text = trim($text);
        
        if (empty($text)) {
            return 0;
        }
        
        // Kelimeleri ayır ve say
        $words = preg_split('/\s+/u', $text);
        return count(array_filter($words, function($word) {
            return !empty(trim($word));
        }));
    }
}

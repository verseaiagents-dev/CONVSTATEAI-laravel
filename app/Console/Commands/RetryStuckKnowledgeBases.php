<?php

namespace App\Console\Commands;

use App\Models\KnowledgeBase;
use App\Jobs\ProcessKnowledgeBaseFile;
use App\Jobs\ProcessKnowledgeBaseUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetryStuckKnowledgeBases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'knowledge-base:retry-stuck {--minutes=30 : Minutes to consider as stuck}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry knowledge bases that are stuck in pending status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minutes = $this->option('minutes');
        $cutoffTime = now()->subMinutes($minutes);
        
        $this->info("Looking for knowledge bases stuck in pending status for more than {$minutes} minutes...");
        
        // Find stuck knowledge bases
        $stuckKnowledgeBases = KnowledgeBase::where('processing_status', 'pending')
            ->where('is_processing', false)
            ->where('created_at', '<', $cutoffTime)
            ->get();
        
        if ($stuckKnowledgeBases->isEmpty()) {
            $this->info('No stuck knowledge bases found.');
            return 0;
        }
        
        $this->info("Found {$stuckKnowledgeBases->count()} stuck knowledge bases.");
        
        $retryCount = 0;
        $errorCount = 0;
        
        foreach ($stuckKnowledgeBases as $knowledgeBase) {
            try {
                $this->info("Retrying knowledge base ID {$knowledgeBase->id} ({$knowledgeBase->name})...");
                
                // Dispatch appropriate job based on source type
                if ($knowledgeBase->source_type === 'file') {
                    ProcessKnowledgeBaseFile::dispatch($knowledgeBase, $knowledgeBase->source_path, $knowledgeBase->file_type)
                        ->onQueue('knowledge-base-processing')
                        ->delay(now()->addSeconds(5));
                } else {
                    ProcessKnowledgeBaseUrl::dispatch($knowledgeBase, $knowledgeBase->source_path)
                        ->onQueue('knowledge-base-processing')
                        ->delay(now()->addSeconds(5));
                }
                
                $retryCount++;
                
                Log::info('Stuck knowledge base retry dispatched', [
                    'knowledge_base_id' => $knowledgeBase->id,
                    'name' => $knowledgeBase->name,
                    'source_type' => $knowledgeBase->source_type,
                    'created_at' => $knowledgeBase->created_at
                ]);
                
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("Failed to retry knowledge base ID {$knowledgeBase->id}: " . $e->getMessage());
                
                Log::error('Failed to retry stuck knowledge base', [
                    'knowledge_base_id' => $knowledgeBase->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        $this->info("Retry completed: {$retryCount} successful, {$errorCount} failed.");
        
        return 0;
    }
}
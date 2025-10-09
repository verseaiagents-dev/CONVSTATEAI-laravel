<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KnowledgeChunk;
use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\DB;

class UpdateKnowledgeChunksProjectId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'knowledge:update-project-ids {--dry-run : Sadece rapor göster, güncelleme yapma}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mevcut KnowledgeChunk kayıtlarının project_id alanlarını ilişkili KnowledgeBase\'den günceller';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 KnowledgeChunk project_id güncellemesi başlatılıyor...');
        $this->newLine();

        // project_id NULL olan chunk'ları bul
        $chunksWithoutProjectId = KnowledgeChunk::whereNull('project_id')
            ->with('knowledgeBase')
            ->get();

        $totalChunks = $chunksWithoutProjectId->count();

        if ($totalChunks === 0) {
            $this->info('✅ Tüm chunk\'ların zaten project_id\'si mevcut!');
            return 0;
        }

        $this->warn("⚠️  {$totalChunks} adet chunk'ta project_id eksik bulundu.");
        $this->newLine();

        // KnowledgeBase bazında gruplama
        $groupedByKnowledgeBase = $chunksWithoutProjectId->groupBy('knowledge_base_id');
        
        $this->info('📊 KnowledgeBase Bazında Özet:');
        $this->table(
            ['Knowledge Base ID', 'Knowledge Base Adı', 'Project ID', 'Chunk Sayısı'],
            $groupedByKnowledgeBase->map(function ($chunks, $kbId) {
                $kb = $chunks->first()->knowledgeBase;
                return [
                    $kbId,
                    $kb->name ?? 'N/A',
                    $kb->project_id ?? 'NULL',
                    $chunks->count()
                ];
            })->values()
        );

        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('🔶 DRY-RUN MODE: Güncelleme yapılmayacak, sadece rapor gösteriliyor.');
            return 0;
        }

        // Kullanıcıdan onay al
        if (!$this->confirm('Bu chunk\'ları güncellemek istiyor musunuz?', true)) {
            $this->info('❌ İşlem iptal edildi.');
            return 0;
        }

        $this->newLine();
        $this->info('🔄 Güncelleme işlemi başlatılıyor...');
        
        $progressBar = $this->output->createProgressBar($totalChunks);
        $progressBar->start();

        $updatedCount = 0;
        $errorCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($chunksWithoutProjectId as $chunk) {
                try {
                    $knowledgeBase = $chunk->knowledgeBase;
                    
                    if (!$knowledgeBase) {
                        $errors[] = "Chunk ID {$chunk->id}: KnowledgeBase bulunamadı";
                        $errorCount++;
                        $progressBar->advance();
                        continue;
                    }

                    if (!$knowledgeBase->project_id) {
                        $errors[] = "Chunk ID {$chunk->id}: KnowledgeBase'de project_id yok (KB ID: {$knowledgeBase->id})";
                        $errorCount++;
                        $progressBar->advance();
                        continue;
                    }

                    $chunk->update([
                        'project_id' => $knowledgeBase->project_id
                    ]);

                    $updatedCount++;
                    $progressBar->advance();

                } catch (\Exception $e) {
                    $errors[] = "Chunk ID {$chunk->id}: " . $e->getMessage();
                    $errorCount++;
                    $progressBar->advance();
                }
            }

            DB::commit();
            $progressBar->finish();

            $this->newLine(2);
            $this->info("✅ Güncelleme tamamlandı!");
            $this->newLine();
            
            $this->table(
                ['Durum', 'Sayı'],
                [
                    ['Toplam Chunk', $totalChunks],
                    ['Başarılı Güncelleme', $updatedCount],
                    ['Hatalı', $errorCount],
                ]
            );

            if ($errorCount > 0) {
                $this->newLine();
                $this->warn("⚠️  {$errorCount} adet hataya rastlandı:");
                foreach ($errors as $error) {
                    $this->line("  • {$error}");
                }
            }

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $progressBar->finish();
            
            $this->newLine(2);
            $this->error('❌ Hata: ' . $e->getMessage());
            $this->error('İşlem geri alındı.');
            
            return 1;
        }
    }
}

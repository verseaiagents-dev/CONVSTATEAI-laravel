<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\WidgetCustomization;
use Illuminate\Support\Facades\Log;

class CreateMissingWidgetCustomizations extends Command
{
    protected $signature = 'widget:create-missing-customizations';
    protected $description = 'Creates missing widget customization records for existing projects';

    public function handle()
    {
        $this->info('Starting to check for missing widget customizations...');
        
        // Tüm projeleri al
        $projects = Project::all();
        $created = 0;
        $skipped = 0;
        
        foreach ($projects as $project) {
            // Her proje için WidgetCustomization kaydı var mı kontrol et
            $hasCustomization = WidgetCustomization::where('project_id', $project->id)
                ->where('user_id', $project->created_by)
                ->exists();
            
            if (!$hasCustomization) {
                try {
                    // Eksik kayıt varsa oluştur
                    WidgetCustomization::create([
                        'user_id' => $project->created_by,
                        'project_id' => $project->id,
                        'customization_token' => hash('sha256', $project->id . time() . uniqid()),
                        'customization_data' => json_encode([])
                    ]);
                    
                    $created++;
                    $this->info("Created customization for project ID: {$project->id}");
                    
                } catch (\Exception $e) {
                    Log::error("Failed to create customization for project ID: {$project->id}", [
                        'error' => $e->getMessage()
                    ]);
                    $this->error("Failed to create customization for project ID: {$project->id}");
                }
            } else {
                $skipped++;
            }
        }
        
        $this->info("Process completed!");
        $this->info("Created: {$created} customizations");
        $this->info("Skipped: {$skipped} (already existed)");
        $this->info("Total projects processed: " . $projects->count());
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\WidgetCustomization;

class CreateMissingWidgetCustomizations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'widget:create-missing-customizations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates missing widget customization records for projects';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to check for missing widget customizations...');

        // Tüm projeleri al
        $projects = Project::all();
        $createdCount = 0;

        foreach ($projects as $project) {
            // Her proje için WidgetCustomization kaydı var mı kontrol et
            $hasCustomization = WidgetCustomization::where('project_id', $project->id)
                ->where('user_id', $project->created_by)
                ->exists();

            if (!$hasCustomization) {
                // Eğer kayıt yoksa oluştur
                WidgetCustomization::create([
                    'user_id' => $project->created_by,
                    'project_id' => $project->id,
                    'customization_token' => hash('sha256', $project->id . time() . uniqid()),
                    'customization_data' => json_encode([])
                ]);

                $createdCount++;
                $this->line("Created customization for project: {$project->name} (ID: {$project->id})");
            }
        }

        $this->info("Completed! Created {$createdCount} new widget customization records.");
        $this->line("Total projects checked: " . $projects->count());
    }
}

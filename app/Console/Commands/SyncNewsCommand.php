<?php

namespace App\Console\Commands;

use App\Services\NewsSyncService;
use Illuminate\Console\Command;

class SyncNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:sync {source?}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync news from all sources or a specific source';

    /**
     * Execute the console command.
     */
    public function handle(NewsSyncService $syncService)
    {
        $sourceName = $this->argument('source');

        if ($sourceName) {
            $source = \App\Models\Source::where('type', $sourceName)->first();

            if (!$source) {
                $this->error("Source not found: {$sourceName}");
                return 1;
            }

            $this->info("Syncing news from {$source->name}...");
            $syncService->syncSource($source);
            $this->info("Sync completed for {$source->name}");
        } else {
            $this->info("Syncing news from all active sources...");
            $syncService->syncAllSources();
            $this->info("Sync completed for all sources");
        }

        return 0;
    }
}

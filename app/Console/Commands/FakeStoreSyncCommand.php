<?php

namespace App\Console\Commands;

use App\Integrator\FakeStore\SyncContext;
use Illuminate\Console\Command;

class FakeStoreSyncCommand extends Command
{
    protected $signature = 'fakestore:sync {--mode=full : Sync mode (full|delta|limited)} {--limit= : Number of products to import (for limited mode)}';
    protected $description = 'Synchronize products from FakeStore API';

    public function __construct(
        private SyncContext $syncContext
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $mode = $this->option('mode');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        
        $message = "Starting FakeStore sync in {$mode} mode";
        if ($limit) {
            $message .= " with limit of {$limit} products";
        }
        $this->info($message . '...');
        
        try {
            $strategy = $this->syncContext->getStrategy($mode, $limit);
            $result = $strategy->sync();
            
            $this->info('Sync completed successfully!');
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Imported', $result->imported],
                    ['Updated', $result->updated],
                    ['Skipped', $result->skipped],
                    ['Errors', count($result->errors)]
                ]
            );
            
            if (!empty($result->errors)) {
                $this->error('Errors occurred during sync:');
                foreach ($result->errors as $error) {
                    $this->line("- {$error['message']}");
                }
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Sync failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}

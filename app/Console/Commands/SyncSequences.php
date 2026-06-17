<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DatabaseSequenceService;

class SyncSequences extends Command
{
    protected $signature = 'db:sync-sequences';

    protected $description = 'Sinkronisasi seluruh sequence PostgreSQL dengan nilai MAX(id) dari masing-masing tabel';

    public function handle(DatabaseSequenceService $sequenceService): int
    {
        $this->info('Memulai sinkronisasi sequence...');

        try {
            $count = $sequenceService->syncSequences();
            $this->info("Sinkronisasi selesai. {$count} sequence diperbarui.");
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Sinkronisasi sequence gagal: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

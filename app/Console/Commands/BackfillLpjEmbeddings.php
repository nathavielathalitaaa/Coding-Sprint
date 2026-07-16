<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackfillLpjEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lpj:backfill-embeddings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill missing embedding vectors for valid LPJs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting LPJ embedding backfill...');

        $lpjs = \App\Models\LaporanPertanggungjawaban::where('status', 'valid')
            ->whereNull('embedding_vector')
            ->get();

        $count = $lpjs->count();

        if ($count === 0) {
            $this->info('No valid LPJs with missing embeddings found. All good!');
            return;
        }

        $this->info("Found {$count} LPJ(s) to process.");

        $bar = $this->output->createProgressBar($count);

        $bar->start();

        foreach ($lpjs as $lpj) {
            \App\Jobs\GenerateLpjEmbedding::dispatch($lpj->id);
            $bar->advance();
        }

        $bar->finish();
        
        $this->newLine();
        $this->info('Backfill completed successfully. Jobs have been dispatched to the queue.');
    }
}

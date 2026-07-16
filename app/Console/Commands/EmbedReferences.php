<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProposalReferenceText;
use App\Services\EmbeddingService;

class EmbedReferences extends Command
{
    protected $signature = 'proposal:embed-references';
    protected $description = 'Generate embedding vectors for all reference texts';

    public function handle(EmbeddingService $embeddingService)
    {
        $records = ProposalReferenceText::whereNull('embedding_vector')->get();

        if ($records->isEmpty()) {
            $this->info("No reference texts require embedding (all are already embedded).");
            return 0;
        }

        $this->info("Found " . $records->count() . " records to embed.");

        $successCount = 0;
        $failCount = 0;

        foreach ($records as $record) {
            $this->info("Generating embedding for section: {$record->section_key} (ID: {$record->id})...");
            
            $vector = $embeddingService->embed($record->contoh_teks);

            if ($vector !== null) {
                $record->update([
                    'embedding_vector' => $vector,
                ]);
                $successCount++;
                $this->info("Success.");
            } else {
                $failCount++;
                $this->error("Failed to retrieve embedding vector from EmbeddingService.");
            }
        }

        $this->info("Embedding process finished.");
        $this->info("Successfully embedded: {$successCount} records.");
        if ($failCount > 0) {
            $this->warn("Failed to embed: {$failCount} records. Check logs or embedding service URL configuration.");
        }

        return 0;
    }
}

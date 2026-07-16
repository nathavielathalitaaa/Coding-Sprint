<?php

namespace App\Jobs;

use App\Models\LaporanPertanggungjawaban;
use App\Services\EmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GenerateLpjEmbedding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The LPJ ID.
     *
     * @var int
     */
    protected int $lpjId;

    /**
     * Create a new job instance.
     *
     * @param int $lpjId
     */
    public function __construct(int $lpjId)
    {
        $this->lpjId = $lpjId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $lpj = LaporanPertanggungjawaban::find($this->lpjId);
        if (!$lpj) {
            Log::warning("GenerateLpjEmbedding: LPJ with ID {$this->lpjId} not found.");
            return;
        }

        // Ensure keywords are present
        $keywords = $lpj->keywords ?? '';
        if (trim($keywords) === '') {
            Log::warning("GenerateLpjEmbedding: LPJ ID {$this->lpjId} has empty keywords.");
            return;
        }

        $embeddingService = new EmbeddingService();
        $vector = $embeddingService->embed($keywords);

        if (is_array($vector) && !empty($vector)) {
            $lpj->update([
                'embedding_vector' => $vector,
                'embedded_at' => Carbon::now(),
            ]);
        } else {
            Log::warning("GenerateLpjEmbedding: Embedding service returned no vector for LPJ ID {$this->lpjId}.");
        }
    }
}

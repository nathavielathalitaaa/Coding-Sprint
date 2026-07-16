<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    /**
     * Generate embedding vector for the given text.
     *
     * @param string $text
     * @return array|null
     */
    public function embed(string $text): ?array
    {
        $url = config('services.embedding.url');
        if (!$url) {
            Log::warning('Embedding service URL is not configured.');
            return null;
        }

        try {
            $response = Http::timeout(10)->post(rtrim($url, '/') . '/embed', [
                'text' => $text,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['vector'] ?? null;
            }

            Log::warning('Embedding service returned non-2xx status code: ' . $response->status(), [
                'response' => $response->body()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::warning('Failed to connect to embedding service: ' . $e->getMessage());
            return null;
        }
    }
}

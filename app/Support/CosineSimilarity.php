<?php

namespace App\Support;

class CosineSimilarity
{
    /**
     * Calculate cosine similarity between two float arrays (vectors).
     *
     * @param array $a
     * @param array $b
     * @return float
     */
    public static function calculate(array $a, array $b): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        $count = min(count($a), count($b));
        if ($count === 0) {
            return 0.0;
        }

        for ($i = 0; $i < $count; $i++) {
            $valA = (float)$a[$i];
            $valB = (float)$b[$i];
            
            $dotProduct += $valA * $valB;
            $normA += $valA * $valA;
            $normB += $valB * $valB;
        }

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}

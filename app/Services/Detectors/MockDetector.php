<?php

namespace App\Services\Detectors;

/**
 * Mock detector for testing when no API keys are configured
 * Returns realistic random scores
 */
class MockDetector implements DetectorInterface
{
    public function getName(): string
    {
        return 'mock';
    }

    public function isAvailable(): bool
    {
        return true; // Always available for testing
    }

    public function analyze(string $text): array
    {
        // Simulate API delay
        usleep(rand(100000, 500000)); // 100-500ms
        
        // Generate somewhat realistic scores based on text characteristics
        $wordCount = str_word_count($text);
        $avgWordLength = strlen(preg_replace('/\s+/', '', $text)) / max($wordCount, 1);
        
        // Longer average word length and more formal text = higher AI probability
        $baseScore = 30 + ($avgWordLength * 5);
        
        // Add randomness
        $aiScore = min(100, max(0, $baseScore + rand(-20, 30)));
        
        return [
            'ai_score' => round($aiScore, 2),
            'human_score' => round(100 - $aiScore, 2),
            'confidence' => round(rand(70, 95), 2),
            'raw_response' => [
                'mock' => true,
                'word_count' => $wordCount,
                'generated_at' => now()->toISOString(),
            ],
        ];
    }
}

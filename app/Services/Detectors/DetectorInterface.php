<?php

namespace App\Services\Detectors;

interface DetectorInterface
{
    /**
     * Get the provider name
     */
    public function getName(): string;

    /**
     * Analyze text for AI content
     * 
     * @param string $text The text to analyze
     * @return array{ai_score: float, human_score: float, confidence: float, raw_response: array}
     */
    public function analyze(string $text): array;

    /**
     * Check if the detector is configured and available
     */
    public function isAvailable(): bool;
}

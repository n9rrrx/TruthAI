<?php

namespace App\Services\Detectors;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SaplingDetector implements DetectorInterface
{
    private string $apiKey;
    private string $baseUrl = 'https://api.sapling.ai/api/v1/aidetect';

    public function __construct()
    {
        $this->apiKey = config('services.sapling.api_key') ?? '';
    }

    public function getName(): string
    {
        return 'sapling';
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function analyze(string $text): array
    {
        if (!$this->isAvailable()) {
            throw new \Exception('Sapling API key not configured');
        }

        try {
            $response = Http::post($this->baseUrl, [
                'key' => $this->apiKey,
                'text' => $text,
            ]);

            if (!$response->successful()) {
                Log::error('Sapling API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Sapling API request failed: ' . $response->status());
            }

            $data = $response->json();
            
            // Sapling returns score 0-1 where higher = more likely AI
            $aiScore = ($data['score'] ?? 0) * 100;

            return [
                'ai_score' => round($aiScore, 2),
                'human_score' => round(100 - $aiScore, 2),
                'confidence' => round(($data['score'] ?? 0.5) * 100, 2),
                'raw_response' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Sapling detection failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}

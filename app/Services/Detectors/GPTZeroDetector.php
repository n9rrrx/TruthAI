<?php

namespace App\Services\Detectors;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GPTZeroDetector implements DetectorInterface
{
    private string $apiKey;
    private string $baseUrl = 'https://api.gptzero.me/v2/predict';

    public function __construct()
    {
        $this->apiKey = config('services.gptzero.api_key') ?? '';
    }

    public function getName(): string
    {
        return 'gptzero';
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function analyze(string $text): array
    {
        if (!$this->isAvailable()) {
            throw new \Exception('GPTZero API key not configured');
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/text', [
                'document' => $text,
            ]);

            if (!$response->successful()) {
                Log::error('GPTZero API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('GPTZero API request failed: ' . $response->status());
            }

            $data = $response->json();
            
            // GPTZero returns completely_generated_prob (0-1)
            $aiProbability = $data['documents'][0]['completely_generated_prob'] ?? 0;
            $aiScore = $aiProbability * 100;

            return [
                'ai_score' => round($aiScore, 2),
                'human_score' => round(100 - $aiScore, 2),
                'confidence' => round(($data['documents'][0]['average_generated_prob'] ?? 0.5) * 100, 2),
                'raw_response' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('GPTZero detection failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}

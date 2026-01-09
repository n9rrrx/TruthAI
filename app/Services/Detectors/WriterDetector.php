<?php

namespace App\Services\Detectors;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WriterDetector implements DetectorInterface
{
    private string $apiKey;
    private string $baseUrl = 'https://enterprise-api.writer.com/content/organization';

    public function __construct()
    {
        $this->apiKey = config('services.writer.api_key') ?? '';
    }

    public function getName(): string
    {
        return 'writer';
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function analyze(string $text): array
    {
        if (!$this->isAvailable()) {
            throw new \Exception('Writer API key not configured');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/detect', [
                'input' => $text,
            ]);

            if (!$response->successful()) {
                Log::error('Writer API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Writer API request failed: ' . $response->status());
            }

            $data = $response->json();
            
            // Writer returns detection result
            $aiScore = ($data['detected'] ?? false) ? 85 : 15;
            if (isset($data['score'])) {
                $aiScore = $data['score'] * 100;
            }

            return [
                'ai_score' => round($aiScore, 2),
                'human_score' => round(100 - $aiScore, 2),
                'confidence' => round(85, 2),
                'raw_response' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Writer detection failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}

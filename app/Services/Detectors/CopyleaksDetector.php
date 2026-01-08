<?php

namespace App\Services\Detectors;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CopyleaksDetector implements DetectorInterface
{
    private string $email;
    private string $apiKey;
    private string $baseUrl = 'https://api.copyleaks.com';
    private ?string $accessToken = null;

    public function __construct()
    {
        $this->email = config('services.copyleaks.email') ?? '';
        $this->apiKey = config('services.copyleaks.api_key') ?? '';
    }

    public function getName(): string
    {
        return 'copyleaks';
    }

    public function isAvailable(): bool
    {
        return !empty($this->email) && !empty($this->apiKey);
    }

    private function authenticate(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $response = Http::post($this->baseUrl . '/v3/account/login/api', [
            'email' => $this->email,
            'key' => $this->apiKey,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Copyleaks authentication failed');
        }

        $this->accessToken = $response->json('access_token');
        return $this->accessToken;
    }

    public function analyze(string $text): array
    {
        if (!$this->isAvailable()) {
            throw new \Exception('Copyleaks credentials not configured');
        }

        try {
            $token = $this->authenticate();

            // Use the AI Content Detection endpoint
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/v2/writer-detector/check', [
                'text' => $text,
            ]);

            if (!$response->successful()) {
                Log::error('Copyleaks API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Copyleaks API request failed');
            }

            $data = $response->json();
            
            // Copyleaks returns AI probability
            $aiScore = ($data['summary']['ai'] ?? 0) * 100;

            return [
                'ai_score' => round($aiScore, 2),
                'human_score' => round(100 - $aiScore, 2),
                'confidence' => round(($data['summary']['confidence'] ?? 0.8) * 100, 2),
                'raw_response' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Copyleaks detection failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}

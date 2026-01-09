<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageDetectorService
{
    private string $apiUser;
    private string $apiSecret;
    private string $baseUrl = 'https://api.sightengine.com/1.0/check.json';

    public function __construct()
    {
        $this->apiUser = config('services.sightengine.api_user') ?? '';
        $this->apiSecret = config('services.sightengine.api_secret') ?? '';
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiUser) && !empty($this->apiSecret);
    }

    /**
     * Analyze image for AI generation and deepfakes
     */
    public function analyzeImage(string $imagePath): array
    {
        if (!$this->isAvailable()) {
            // Fall back to basic analysis without API
            return $this->basicAnalysis($imagePath);
        }

        try {
            // Check if it's a URL or file path
            if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
                return $this->analyzeImageUrl($imagePath);
            } else {
                return $this->analyzeImageFile($imagePath);
            }
        } catch (\Exception $e) {
            Log::error('Image analysis failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Analyze image from URL
     */
    private function analyzeImageUrl(string $url): array
    {
        $response = Http::get($this->baseUrl, [
            'url' => $url,
            'models' => 'genai,deepfake',
            'api_user' => $this->apiUser,
            'api_secret' => $this->apiSecret,
        ]);

        if (!$response->successful()) {
            throw new \Exception('SightEngine API request failed: ' . $response->status());
        }

        return $this->parseResponse($response->json());
    }

    /**
     * Analyze image from file
     */
    private function analyzeImageFile(string $filePath): array
    {
        $response = Http::attach(
            'media', file_get_contents($filePath), basename($filePath)
        )->post($this->baseUrl, [
            'models' => 'genai,deepfake',
            'api_user' => $this->apiUser,
            'api_secret' => $this->apiSecret,
        ]);

        if (!$response->successful()) {
            throw new \Exception('SightEngine API request failed: ' . $response->status());
        }

        return $this->parseResponse($response->json());
    }

    /**
     * Parse SightEngine response
     */
    private function parseResponse(array $data): array
    {
        $aiGenerated = $data['type']['ai_generated'] ?? 0;
        $deepfake = $data['type']['deepfake'] ?? 0;
        
        // Calculate overall AI score (max of both)
        $aiScore = max($aiGenerated, $deepfake) * 100;
        
        $verdict = 'Likely Real';
        if ($aiScore >= 70) {
            $verdict = 'Likely AI Generated';
        } elseif ($aiScore >= 30) {
            $verdict = 'Possibly AI Generated';
        }

        return [
            'ai_score' => round($aiScore, 2),
            'human_score' => round(100 - $aiScore, 2),
            'ai_generated_probability' => round($aiGenerated * 100, 2),
            'deepfake_probability' => round($deepfake * 100, 2),
            'verdict' => $verdict,
            'raw_response' => $data,
        ];
    }

    /**
     * Basic analysis without API (checks metadata)
     */
    private function basicAnalysis(string $imagePath): array
    {
        // Without API, we can only do basic checks
        // This is a placeholder that returns a mock result
        return [
            'ai_score' => 0,
            'human_score' => 100,
            'ai_generated_probability' => 0,
            'deepfake_probability' => 0,
            'verdict' => 'Unable to analyze (no API key)',
            'note' => 'Add SightEngine API credentials for accurate detection',
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Scan;
use App\Models\ScanResult;
use App\Services\Detectors\DetectorInterface;
use App\Services\Detectors\GPTZeroDetector;
use App\Services\Detectors\CopyleaksDetector;
use App\Services\Detectors\MockDetector;
use Illuminate\Support\Facades\Log;

class DetectorService
{
    /**
     * Configured detector weights for consensus
     */
    private array $weights = [
        'gptzero' => 0.40,
        'copyleaks' => 0.35,
        'mock' => 0.25,
    ];

    /**
     * Get all available detectors
     */
    public function getDetectors(): array
    {
        $detectors = [
            new GPTZeroDetector(),
            new CopyleaksDetector(),
        ];

        // Filter to only available detectors
        $available = array_filter($detectors, fn($d) => $d->isAvailable());

        // If no real detectors available, use mock
        if (empty($available)) {
            $available = [new MockDetector()];
        }

        return $available;
    }

    /**
     * Run detection on text using all available providers
     */
    public function detect(Scan $scan): Scan
    {
        $scan->update(['status' => 'processing']);
        
        $detectors = $this->getDetectors();
        $results = [];
        $totalWeight = 0;
        $weightedScore = 0;

        foreach ($detectors as $detector) {
            try {
                $result = $detector->analyze($scan->content);
                
                // Store individual result
                $scanResult = ScanResult::create([
                    'scan_id' => $scan->id,
                    'provider' => $detector->getName(),
                    'ai_score' => $result['ai_score'],
                    'human_score' => $result['human_score'],
                    'confidence' => $result['confidence'],
                    'raw_response' => $result['raw_response'],
                    'status' => 'success',
                ]);

                $results[] = $scanResult;
                
                // Calculate weighted score
                $weight = $this->weights[$detector->getName()] ?? 0.25;
                $weightedScore += $result['ai_score'] * $weight;
                $totalWeight += $weight;

            } catch (\Exception $e) {
                Log::error('Detector failed', [
                    'detector' => $detector->getName(),
                    'error' => $e->getMessage(),
                ]);

                ScanResult::create([
                    'scan_id' => $scan->id,
                    'provider' => $detector->getName(),
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        // Calculate final consensus score
        $finalScore = $totalWeight > 0 ? $weightedScore / $totalWeight : 0;

        $scan->update([
            'ai_score' => round($finalScore, 2),
            'human_score' => round(100 - $finalScore, 2),
            'status' => 'completed',
        ]);

        return $scan->fresh(['results']);
    }

    /**
     * Get list of configured providers
     */
    public function getProviderStatus(): array
    {
        return [
            'gptzero' => [
                'name' => 'GPTZero',
                'available' => (new GPTZeroDetector())->isAvailable(),
                'weight' => $this->weights['gptzero'],
            ],
            'copyleaks' => [
                'name' => 'Copyleaks',
                'available' => (new CopyleaksDetector())->isAvailable(),
                'weight' => $this->weights['copyleaks'],
            ],
            'mock' => [
                'name' => 'Mock (Testing)',
                'available' => true,
                'weight' => $this->weights['mock'],
            ],
        ];
    }
}

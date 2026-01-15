<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlagiarismService
{
    private string $apiKey;
    private string $engineId;

    public function __construct()
    {
        $this->apiKey = config('services.google_search.api_key', '');
        $this->engineId = config('services.google_search.engine_id', '');
    }

    public function check(string $text): array
    {
        $sentences = $this->extractSentences($text);
        
        if (count($sentences) === 0) {
            return [
                'plagiarism_score' => 0,
                'original_score' => 100,
                'matches' => [],
                'checked_sentences' => 0,
            ];
        }

        // Check if API is configured
        if (empty($this->apiKey) || empty($this->engineId)) {
            Log::warning('Plagiarism check: Google Search API not configured');
            return [
                'plagiarism_score' => 0,
                'original_score' => 100,
                'matches' => [],
                'checked_sentences' => 0,
                'error' => 'API not configured',
            ];
        }

        $matches = [];
        $plagiarizedCount = 0;
        // Check up to 5 sentences (API has 100 free queries/day limit)
        $sentencesToCheck = array_slice($sentences, 0, 5);
        $actuallyChecked = 0;

        foreach ($sentencesToCheck as $sentence) {
            // Skip short sentences
            if (str_word_count($sentence) < 6) continue;

            $actuallyChecked++;
            $result = $this->searchWithGoogleAPI($sentence);
            
            if ($result['found']) {
                $plagiarizedCount++;
                $matches[] = [
                    'sentence' => $sentence,
                    'sources' => $result['sources'],
                ];
            }
            
            // Small delay to avoid rate limiting
            usleep(200000); // 200ms
        }

        $plagiarismScore = $actuallyChecked > 0 
            ? round(($plagiarizedCount / $actuallyChecked) * 100) 
            : 0;

        return [
            'plagiarism_score' => $plagiarismScore,
            'original_score' => 100 - $plagiarismScore,
            'matches' => $matches,
            'checked_sentences' => $actuallyChecked,
            'plagiarized_sentences' => $plagiarizedCount,
        ];
    }

    private function extractSentences(string $text): array
    {
        // Split on sentence-ending punctuation
        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        return array_values(array_filter(
            array_map(fn($s) => trim(preg_replace('/\s+/', ' ', $s)), $sentences), 
            fn($s) => strlen($s) > 30
        ));
    }

    /**
     * Search using Google Custom Search JSON API
     * Official API - no blocking, returns real sources
     */
    private function searchWithGoogleAPI(string $sentence): array
    {
        try {
            // Take first 10 words for the search phrase
            $words = explode(' ', $sentence);
            $searchPhrase = implode(' ', array_slice($words, 0, min(10, count($words))));
            
            // Exact phrase search with quotes
            $query = '"' . $searchPhrase . '"';
            
            Log::info('Plagiarism: Searching for phrase', ['query' => $query]);

            $response = Http::timeout(15)->get('https://www.googleapis.com/customsearch/v1', [
                'key' => $this->apiKey,
                'cx' => $this->engineId,
                'q' => $query,
                'num' => 5, // Get up to 5 results
            ]);

            if (!$response->successful()) {
                Log::warning('Google Search API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return ['found' => false, 'sources' => []];
            }

            $data = $response->json();
            $sources = [];
            
            // Check if we have search results
            if (isset($data['items']) && count($data['items']) > 0) {
                foreach ($data['items'] as $item) {
                    // Skip if no link
                    if (empty($item['link'])) continue;
                    
                    $sources[] = [
                        'title' => $item['title'] ?? parse_url($item['link'], PHP_URL_HOST),
                        'url' => $item['link'],
                        'snippet' => $item['snippet'] ?? '',
                    ];
                    
                    // Limit to 3 sources per sentence
                    if (count($sources) >= 3) break;
                }
            }
            
            if (count($sources) > 0) {
                Log::info('Plagiarism: Found sources', [
                    'count' => count($sources),
                    'first_source' => $sources[0]['url'] ?? 'none',
                ]);
                return ['found' => true, 'sources' => $sources];
            }
            
            // No results = content is original
            Log::info('Plagiarism: No matches found - content appears original');
            return ['found' => false, 'sources' => []];
            
        } catch (\Exception $e) {
            Log::warning('Plagiarism search failed', ['error' => $e->getMessage()]);
            return ['found' => false, 'sources' => []];
        }
    }

    public function isAvailable(): bool 
    { 
        return !empty($this->apiKey) && !empty($this->engineId); 
    }
    
    public function getStatus(): array 
    { 
        return [
            'available' => $this->isAvailable(), 
            'provider' => 'Google Custom Search API',
        ]; 
    }
}

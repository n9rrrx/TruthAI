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

    /**
     * Check text for plagiarism
     * @param string $text The text to check
     * @param bool $deepScan If true, checks more sentences (up to 10)
     */
    public function check(string $text, bool $deepScan = false): array
    {
        $sentences = $this->extractSentences($text);
        
        if (count($sentences) === 0) {
            return [
                'plagiarism_score' => 0,
                'original_score' => 100,
                'matches' => [],
                'checked_sentences' => 0,
                'total_sentences' => 0,
                'all_sentences' => [],
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
                'total_sentences' => count($sentences),
                'all_sentences' => [],
                'error' => 'API not configured',
            ];
        }

        $matches = [];
        $plagiarizedCount = 0;
        $allSentences = []; // Track all sentences with their status
        
        // Deep scan checks more sentences (10 vs 5)
        $maxSentences = $deepScan ? 10 : 5;
        $sentencesToCheck = array_slice($sentences, 0, $maxSentences);
        $actuallyChecked = 0;

        foreach ($sentencesToCheck as $index => $sentence) {
            // Skip short sentences
            if (str_word_count($sentence) < 6) {
                $allSentences[] = [
                    'text' => $sentence,
                    'status' => 'skipped',
                    'sources' => [],
                ];
                continue;
            }

            $actuallyChecked++;
            $result = $this->searchWithGoogleAPI($sentence);
            
            if ($result['found']) {
                $plagiarizedCount++;
                $matches[] = [
                    'sentence' => $sentence,
                    'sources' => $result['sources'],
                    'similarity' => $this->calculateSimilarity($sentence, $result['sources']),
                ];
                $allSentences[] = [
                    'text' => $sentence,
                    'status' => 'plagiarized',
                    'sources' => $result['sources'],
                    'similarity' => $this->calculateSimilarity($sentence, $result['sources']),
                ];
            } else {
                $allSentences[] = [
                    'text' => $sentence,
                    'status' => 'original',
                    'sources' => [],
                ];
            }
            
            // Small delay to avoid rate limiting
            usleep(200000); // 200ms
        }

        // Mark remaining sentences as unchecked
        for ($i = count($sentencesToCheck); $i < count($sentences); $i++) {
            $allSentences[] = [
                'text' => $sentences[$i],
                'status' => 'unchecked',
                'sources' => [],
            ];
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
            'total_sentences' => count($sentences),
            'all_sentences' => $allSentences,
            'deep_scan' => $deepScan,
        ];
    }

    /**
     * Calculate similarity percentage based on snippet match
     */
    private function calculateSimilarity(string $sentence, array $sources): int
    {
        if (empty($sources)) return 0;
        
        $sentenceWords = array_map('strtolower', explode(' ', $sentence));
        $maxSimilarity = 0;
        
        foreach ($sources as $source) {
            if (empty($source['snippet'])) continue;
            
            $snippetWords = array_map('strtolower', explode(' ', $source['snippet']));
            $matchingWords = count(array_intersect($sentenceWords, $snippetWords));
            $similarity = count($sentenceWords) > 0 
                ? round(($matchingWords / count($sentenceWords)) * 100) 
                : 0;
            
            $maxSimilarity = max($maxSimilarity, $similarity);
        }
        
        // Boost similarity if exact phrase found (since we search with quotes)
        return min(100, $maxSimilarity + 20);
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
                        'domain' => parse_url($item['link'], PHP_URL_HOST) ?? '',
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

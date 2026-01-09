<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';
    private string $model = 'llama-3.3-70b-versatile';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key') ?? '';
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Humanize text using Llama 3.3
     */
    public function humanize(string $text, string $level = 'medium', string $style = 'academic'): string
    {
        if (!$this->isAvailable()) {
            throw new \Exception('Groq API key not configured');
        }

        $intensity = match($level) {
            'light' => 'Light edit: Change about 20% of words while keeping structure',
            'medium' => 'Moderate rewrite: Change sentence structures and 40% of vocabulary',
            'strong' => 'Complete rewrite: Entirely new phrasing, different paragraph structure, 60%+ changes',
        };

        $styleGuide = match($style) {
            'casual' => 'Write like texting a friend - use "gonna", "kinda", abbreviations, and informal speech',
            'professional' => 'Write like a business email - clear but with some personality',
            'creative' => 'Write with flair - use metaphors, unique expressions, show emotion',
            default => 'Write like a college student essay - knowledgeable but not robotic',
        };

        $prompt = <<<PROMPT
You are a human writer. Completely rewrite this text as if YOU personally wrote it from scratch. 

CRITICAL RULES (follow these exactly):
1. Start some sentences with "I think", "Honestly", "Look," or "The thing is"
2. Use contractions everywhere (don't, won't, it's, that's, there's)
3. Vary sentence lengths dramatically - mix 5-word sentences with 25-word sentences  
4. Add 1-2 minor grammatical "mistakes" like starting with "And" or "But"
5. Include personal opinions like "which is pretty crazy" or "not gonna lie"
6. Avoid these AI words: "crucial", "utilize", "comprehensive", "facilitate", "leverage", "robust"
7. Use simple words: "use" not "utilize", "help" not "facilitate", "strong" not "robust"
8. Break some formal rules - fragment sentences are OK, ending with prepositions is fine
9. Add filler phrases: "you know", "I mean", "basically", "kind of"

Level: $intensity
Style: $styleGuide

ORIGINAL TEXT:
$text

YOUR REWRITTEN VERSION (just the text, no explanation):
PROMPT;

        try {
            // First pass - creative rewrite
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 1.1, // Higher for more creativity
                'max_tokens' => 4096,
                'top_p' => 0.95,
            ]);

            if (!$response->successful()) {
                Log::error('Groq API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Groq API request failed');
            }

            $data = $response->json();
            $firstPass = $data['choices'][0]['message']['content'] ?? $text;

            // For strong level, do a second pass
            if ($level === 'strong') {
                $secondPrompt = <<<PROMPT2
Take this text and make it sound even MORE casual and human. Add typos, slang, run-on sentences. Make it messy like real human writing:

$firstPass

Just output the rewritten text:
PROMPT2;

                $response2 = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(60)->post($this->baseUrl, [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'user', 'content' => $secondPrompt],
                    ],
                    'temperature' => 1.2,
                    'max_tokens' => 4096,
                ]);

                if ($response2->successful()) {
                    $data2 = $response2->json();
                    return $data2['choices'][0]['message']['content'] ?? $firstPass;
                }
            }

            return $firstPass;

        } catch (\Exception $e) {
            Log::error('Groq humanization failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}

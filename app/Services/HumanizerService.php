<?php

namespace App\Services;

use App\Models\Scan;
use Illuminate\Support\Facades\Log;

class HumanizerService
{
    private ?GroqService $groqService = null;

    public function __construct()
    {
        $this->groqService = new GroqService();
    }

    /**
     * Humanization techniques by level
     */
    private array $techniques = [
        'light' => [
            'synonym_replacement' => 0.15,
            'sentence_restructure' => 0.10,
            'add_filler_words' => 0.05,
        ],
        'medium' => [
            'synonym_replacement' => 0.30,
            'sentence_restructure' => 0.25,
            'add_filler_words' => 0.15,
            'vary_sentence_length' => 0.20,
        ],
        'strong' => [
            'synonym_replacement' => 0.50,
            'sentence_restructure' => 0.40,
            'add_filler_words' => 0.25,
            'vary_sentence_length' => 0.35,
            'add_personal_phrases' => 0.30,
        ],
    ];

    /**
     * Common synonyms for AI-typical words
     */
    private array $synonyms = [
        'utilize' => ['use', 'employ', 'work with', 'rely on'],
        'implement' => ['put in place', 'set up', 'introduce', 'apply'],
        'furthermore' => ['also', 'plus', 'on top of that', 'what\'s more'],
        'however' => ['but', 'though', 'still', 'yet', 'that said'],
        'therefore' => ['so', 'because of this', 'that\'s why', 'as a result'],
        'additionally' => ['also', 'plus', 'and', 'besides'],
        'consequently' => ['so', 'as a result', 'because of this'],
        'nevertheless' => ['still', 'even so', 'but', 'yet'],
        'subsequently' => ['then', 'after that', 'later', 'next'],
        'demonstrates' => ['shows', 'proves', 'makes clear'],
        'indicates' => ['shows', 'suggests', 'points to'],
        'significant' => ['major', 'big', 'important', 'notable'],
        'substantial' => ['big', 'large', 'considerable', 'major'],
        'comprehensive' => ['complete', 'thorough', 'full', 'detailed'],
        'facilitate' => ['help', 'make easier', 'enable', 'support'],
        'enhance' => ['improve', 'boost', 'strengthen', 'make better'],
        'optimal' => ['best', 'ideal', 'perfect'],
        'crucial' => ['key', 'vital', 'essential', 'important'],
        'paramount' => ['most important', 'key', 'crucial', 'vital'],
        'endeavor' => ['try', 'attempt', 'effort', 'work'],
        'ascertain' => ['find out', 'discover', 'determine', 'learn'],
        'elucidate' => ['explain', 'clarify', 'make clear'],
        'articulate' => ['express', 'say', 'explain', 'put into words'],
    ];

    /**
     * Filler phrases for natural human speech
     */
    private array $fillerPhrases = [
        'To be honest, ',
        'Honestly, ',
        'The truth is, ',
        'I think ',
        'In my view, ',
        'The way I see it, ',
        'You know, ',
        'Look, ',
        'Here\'s the thing - ',
        'What I mean is, ',
        'Basically, ',
        'Essentially, ',
    ];

    /**
     * Sentence starters for variety
     */
    private array $sentenceStarters = [
        'Now, ',
        'So, ',
        'Well, ',
        'Actually, ',
        'Thing is, ',
        'See, ',
        'Right, ',
    ];

    /**
     * Humanize text based on level - uses AI when available
     */
    public function humanize(string $text, string $level = 'medium', string $style = 'academic'): array
    {
        $level = in_array($level, ['light', 'medium', 'strong']) ? $level : 'medium';
        
        // Try Groq AI-powered humanization first
        if ($this->groqService && $this->groqService->isAvailable()) {
            try {
                $humanizedText = $this->groqService->humanize($text, $level, $style);
                return [
                    'original' => $text,
                    'humanized' => $humanizedText,
                    'word_count' => str_word_count($humanizedText),
                    'level' => $level,
                    'style' => $style,
                    'method' => 'ai',
                ];
            } catch (\Exception $e) {
                Log::warning('Groq humanization failed, falling back to local', ['error' => $e->getMessage()]);
            }
        }
        
        // Fallback to local processing
        return $this->humanizeLocal($text, $level, $style);
    }

    /**
     * Local humanization (fallback when AI not available)
     */
    private function humanizeLocal(string $text, string $level, string $style): array
    {
        $sentences = $this->splitIntoSentences($text);
        $humanizedSentences = [];
        
        foreach ($sentences as $index => $sentence) {
            $humanized = $this->humanizeSentence($sentence, $level, $index, count($sentences));
            $humanizedSentences[] = $humanized;
        }
        
        $humanizedText = implode(' ', $humanizedSentences);
        
        // Apply style adjustments
        $humanizedText = $this->applyStyle($humanizedText, $style);
        
        // Clean up extra spaces
        $humanizedText = preg_replace('/\s+/', ' ', $humanizedText);
        $humanizedText = trim($humanizedText);

        return [
            'original' => $text,
            'humanized' => $humanizedText,
            'word_count' => str_word_count($humanizedText),
            'level' => $level,
            'style' => $style,
            'method' => 'local',
        ];
    }

    /**
     * Humanize a single sentence
     */
    private function humanizeSentence(string $sentence, string $level, int $index, int $totalSentences): string
    {
        $sentence = trim($sentence);
        if (empty($sentence)) return '';

        $techniques = $this->techniques[$level];
        
        // Apply synonym replacement
        if (rand(1, 100) <= ($techniques['synonym_replacement'] ?? 0) * 100) {
            $sentence = $this->replaceSynonyms($sentence);
        }
        
        // Add filler phrases at start of some sentences
        if (isset($techniques['add_filler_words']) && rand(1, 100) <= $techniques['add_filler_words'] * 100) {
            if ($index > 0 && $index < $totalSentences - 1) { // Not first or last sentence
                $filler = $this->fillerPhrases[array_rand($this->fillerPhrases)];
                $sentence = $filler . lcfirst($sentence);
            }
        }
        
        // Add sentence starters for variety
        if (isset($techniques['vary_sentence_length']) && rand(1, 100) <= $techniques['vary_sentence_length'] * 100) {
            if ($index > 0 && rand(1, 3) === 1) {
                $starter = $this->sentenceStarters[array_rand($this->sentenceStarters)];
                $sentence = $starter . lcfirst($sentence);
            }
        }
        
        // Restructure some sentences (simple transformations)
        if (isset($techniques['sentence_restructure']) && rand(1, 100) <= $techniques['sentence_restructure'] * 100) {
            $sentence = $this->restructureSentence($sentence);
        }
        
        return $sentence;
    }

    /**
     * Replace AI-typical words with more human synonyms
     */
    private function replaceSynonyms(string $text): string
    {
        foreach ($this->synonyms as $formal => $replacements) {
            if (stripos($text, $formal) !== false) {
                $replacement = $replacements[array_rand($replacements)];
                $text = preg_replace('/\b' . preg_quote($formal, '/') . '\b/i', $replacement, $text, 1);
            }
        }
        return $text;
    }

    /**
     * Simple sentence restructuring
     */
    private function restructureSentence(string $sentence): string
    {
        // Move "It is important to note that" type phrases
        $patterns = [
            '/^It is important to note that /i' => '',
            '/^It should be noted that /i' => '',
            '/^It is worth mentioning that /i' => '',
            '/^One could argue that /i' => '',
            '/^It can be said that /i' => '',
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            if (preg_match($pattern, $sentence)) {
                $sentence = preg_replace($pattern, $replacement, $sentence);
                $sentence = ucfirst($sentence);
                break;
            }
        }
        
        return $sentence;
    }

    /**
     * Apply writing style adjustments
     */
    private function applyStyle(string $text, string $style): string
    {
        switch ($style) {
            case 'casual':
                $text = str_ireplace(
                    ['cannot', 'will not', 'do not', 'is not', 'are not', 'would not'],
                    ["can't", "won't", "don't", "isn't", "aren't", "wouldn't"],
                    $text
                );
                break;
            case 'professional':
                // Keep formal but add some contractions
                $text = str_ireplace(
                    ['cannot', 'will not'],
                    ["can't", "won't"],
                    $text
                );
                break;
            case 'creative':
                // Add more expressive language
                $text = str_ireplace(
                    ['good', 'bad', 'important'],
                    ['fantastic', 'terrible', 'crucial'],
                    $text
                );
                break;
            // academic - keep as is (formal)
        }
        
        return $text;
    }

    /**
     * Split text into sentences
     */
    private function splitIntoSentences(string $text): array
    {
        // Split on sentence endings but keep the punctuation
        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        return $sentences ?: [$text];
    }

    /**
     * Create humanization scan record
     */
    public function createScan(int $userId, string $content, string $level, string $style): Scan
    {
        $result = $this->humanize($content, $level, $style);
        
        $scan = Scan::create([
            'user_id' => $userId,
            'type' => 'humanize',
            'content' => $content,
            'humanized_text' => $result['humanized'],
            'status' => 'completed',
            'metadata' => [
                'level' => $level,
                'style' => $style,
                'original_word_count' => str_word_count($content),
                'humanized_word_count' => $result['word_count'],
            ],
        ]);
        
        return $scan;
    }
}

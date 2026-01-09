<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Services\DetectorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    private DetectorService $detectorService;

    public function __construct(DetectorService $detectorService)
    {
        $this->detectorService = $detectorService;
    }

    /**
     * Create a new scan and run detection
     */
    public function store(Request $request)
    {
        $type = $request->input('type', 'text');
        
        // Different validation for URL vs text
        if ($type === 'url') {
            $request->validate([
                'content' => 'required|url',
                'type' => 'in:text,url',
            ]);
        } else {
            $request->validate([
                'content' => 'required|string|min:50',
                'type' => 'in:text,url',
            ]);
        }

        $user = Auth::user();
        
        // Check daily limit (100 scans/day for free users)
        $todayCount = $user->scans()->whereDate('created_at', today())->count();
        if ($todayCount >= 100) {
            return response()->json([
                'error' => 'Daily scan limit reached. Upgrade for unlimited scans.',
            ], 429);
        }

        try {
            $content = $request->input('content');
            $title = null;
            
            // If URL type, fetch content from the URL
            if ($type === 'url') {
                $urlContent = $this->fetchUrlContent($content);
                $title = $urlContent['title'] ?? parse_url($content, PHP_URL_HOST);
                $content = $urlContent['content'];
                
                if (strlen($content) < 50) {
                    return response()->json([
                        'error' => 'Could not extract enough text from the URL. Please try a different page.',
                    ], 422);
                }
            }

            // Create the scan
            $scan = Scan::create([
                'user_id' => $user->id,
                'type' => $type,
                'content' => $content,
                'title' => $title,
                'status' => 'pending',
                'metadata' => $type === 'url' ? ['source_url' => $request->input('content')] : null,
            ]);

            // Run detection
            $scan = $this->detectorService->detect($scan);

            return response()->json([
                'success' => true,
                'scan' => [
                    'id' => $scan->id,
                    'ai_score' => $scan->ai_score ?? 0,
                    'human_score' => $scan->human_score ?? 100,
                    'verdict' => $scan->verdict,
                    'word_count' => $scan->word_count,
                    'status' => $scan->status,
                    'title' => $scan->title,
                    'results' => $scan->results->map(fn($r) => [
                        'provider' => $r->provider,
                        'provider_name' => $r->provider_name,
                        'ai_score' => $r->ai_score ?? 0,
                        'confidence' => $r->confidence ?? 0,
                        'status' => $r->status,
                    ]),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Scan detection error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'error' => 'Detection failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetch text content from a URL
     */
    private function fetchUrlContent(string $url): array
    {
        $html = null;
        
        // Try direct fetch first
        try {
            $response = \Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Accept-Encoding' => 'gzip, deflate',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
                'Cache-Control' => 'max-age=0',
            ])->timeout(15)->get($url);
            
            if ($response->successful()) {
                $html = $response->body();
            }
        } catch (\Exception $e) {
            // Direct fetch failed, will try fallback
        }

        // Fallback 1: Use webcache.googleusercontent.com (Google Cache)
        if (!$html) {
            try {
                $cacheUrl = 'https://webcache.googleusercontent.com/search?q=cache:' . urlencode($url) . '&strip=1';
                $response = \Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])->timeout(15)->get($cacheUrl);
                
                if ($response->successful()) {
                    $html = $response->body();
                }
            } catch (\Exception $e) {
                // Google cache failed
            }
        }

        // Fallback 2: Use api.allorigins.win
        if (!$html) {
            try {
                $proxyUrl = 'https://api.allorigins.win/raw?url=' . urlencode($url);
                $response = \Http::timeout(20)->get($proxyUrl);
                
                if ($response->successful()) {
                    $html = $response->body();
                }
            } catch (\Exception $e) {
                // Proxy failed
            }
        }

        // Fallback 3: Use thingproxy
        if (!$html) {
            try {
                $proxyUrl = 'https://thingproxy.freeboard.io/fetch/' . $url;
                $response = \Http::timeout(20)->get($proxyUrl);
                
                if ($response->successful()) {
                    $html = $response->body();
                }
            } catch (\Exception $e) {
                // Proxy failed
            }
        }

        // Fallback 4: Use corsproxy.io
        if (!$html) {
            try {
                $proxyUrl = 'https://corsproxy.io/?' . urlencode($url);
                $response = \Http::timeout(20)->get($proxyUrl);
                
                if ($response->successful()) {
                    $html = $response->body();
                }
            } catch (\Exception $e) {
                // All methods failed
            }
        }

        if (!$html) {
            throw new \Exception('Could not fetch URL content. The website has strong anti-bot protection. Please copy and paste the article text directly.');
        }
        
        // Extract title
        preg_match('/<title>(.*?)<\/title>/is', $html, $titleMatch);
        $title = isset($titleMatch[1]) ? html_entity_decode(trim($titleMatch[1])) : null;
        
        // Remove script, style, nav, header, footer tags
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
        $html = preg_replace('/<nav[^>]*>.*?<\/nav>/is', '', $html);
        $html = preg_replace('/<header[^>]*>.*?<\/header>/is', '', $html);
        $html = preg_replace('/<footer[^>]*>.*?<\/footer>/is', '', $html);
        $html = preg_replace('/<aside[^>]*>.*?<\/aside>/is', '', $html);
        
        // Get text from article or main content
        if (preg_match('/<article[^>]*>(.*?)<\/article>/is', $html, $articleMatch)) {
            $content = $articleMatch[1];
        } elseif (preg_match('/<div[^>]*class="[^"]*(?:article|content|post|entry)[^"]*"[^>]*>(.*?)<\/div>/is', $html, $contentMatch)) {
            $content = $contentMatch[1];
        } elseif (preg_match('/<main[^>]*>(.*?)<\/main>/is', $html, $mainMatch)) {
            $content = $mainMatch[1];
        } elseif (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $bodyMatch)) {
            $content = $bodyMatch[1];
        } else {
            $content = $html;
        }
        
        // Strip HTML tags and clean up
        $content = strip_tags($content);
        $content = html_entity_decode($content);
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);
        
        return [
            'title' => $title,
            'content' => $content,
        ];
    }


    /**
     * Get scan details
     */
    public function show(Scan $scan)
    {
        // Ensure user owns the scan
        if ($scan->user_id !== Auth::id()) {
            abort(403);
        }

        return response()->json([
            'scan' => [
                'id' => $scan->id,
                'type' => $scan->type,
                'title' => $scan->title,
                'content' => $scan->content,
                'ai_score' => $scan->ai_score,
                'human_score' => $scan->human_score,
                'verdict' => $scan->verdict,
                'word_count' => $scan->word_count,
                'status' => $scan->status,
                'created_at' => $scan->created_at->toISOString(),
                'results' => $scan->results->map(fn($r) => [
                    'provider' => $r->provider,
                    'provider_name' => $r->provider_name,
                    'ai_score' => $r->ai_score,
                    'human_score' => $r->human_score,
                    'confidence' => $r->confidence,
                    'status' => $r->status,
                ]),
            ],
        ]);
    }

    /**
     * Delete a scan
     */
    public function destroy(Scan $scan)
    {
        if ($scan->user_id !== Auth::id()) {
            abort(403);
        }

        $scan->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get provider status
     */
    public function providers()
    {
        return response()->json([
            'providers' => $this->detectorService->getProviderStatus(),
        ]);
    }
}

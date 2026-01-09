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
        try {
            $response = \Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
            ])->timeout(20)->get($url);
            
            if (!$response->successful()) {
                if ($response->status() === 403) {
                    throw new \Exception('This website blocks automated access. Please copy and paste the article text directly instead.');
                }
                throw new \Exception('Website returned error: ' . $response->status());
            }

            $html = $response->body();
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception('Could not connect to the website. It may be blocking our request.');
        }
        
        // Extract title
        preg_match('/<title>(.*?)<\/title>/is', $html, $titleMatch);
        $title = isset($titleMatch[1]) ? html_entity_decode(trim($titleMatch[1])) : null;
        
        // Remove script and style tags
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
        
        // Get text from body or article
        if (preg_match('/<article[^>]*>(.*?)<\/article>/is', $html, $articleMatch)) {
            $content = $articleMatch[1];
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

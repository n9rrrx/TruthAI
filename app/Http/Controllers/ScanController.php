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
        $request->validate([
            'content' => 'required|string|min:50',
            'type' => 'in:text',
        ]);

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

            // Create the scan
            \Log::info('Creating scan', ['user' => $user->id, 'type' => $type, 'content_length' => strlen($content)]);
            
            $scan = Scan::create([
                'user_id' => $user->id,
                'type' => 'text',
                'content' => $content,
                'title' => $title,
                'status' => 'pending',
            ]);

            \Log::info('Scan created, running detection', ['scan_id' => $scan->id]);

            // Run detection
            $scan = $this->detectorService->detect($scan);

            \Log::info('Detection complete', ['scan_id' => $scan->id, 'ai_score' => $scan->ai_score]);

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
     * Fetch text content from a URL using headless browser (Browsershot)
     * This renders JavaScript before extracting text, so it works with
     * dynamic sites like Dawn.com
     */
    private function fetchUrlContent(string $url): array
    {
        \Log::info('Fetching URL with headless browser', ['url' => $url]);
        
        $html = null;
        $title = null;
        
        // Try Browsershot (headless Chrome) first - this renders JavaScript
        try {
            $browsershot = \Spatie\Browsershot\Browsershot::url($url)
                ->setNodeBinary(config('services.browsershot.node_path', 'node'))
                ->setNpmBinary(config('services.browsershot.npm_path', 'npm'))
                ->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36')
                ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox'])
                ->waitUntilNetworkIdle()
                ->timeout(20);
            
            // Get rendered HTML after JavaScript execution
            $html = $browsershot->bodyHtml();
            
            // Check if we got a CAPTCHA page instead of real content
            if (stripos($html, 'verify you are human') !== false || 
                stripos($html, 'captcha') !== false ||
                stripos($html, 'checking your browser') !== false ||
                stripos($html, 'cloudflare') !== false) {
                
                \Log::warning('Site showed CAPTCHA/bot protection', ['url' => $url]);
                // Let it fall through to HTTP fallback or throw
                $html = null;
            } else {
                \Log::info('Browsershot fetched HTML', ['length' => strlen($html)]);
            }
            
        } catch (\Exception $e) {
            \Log::warning('Browsershot failed, falling back to HTTP', ['error' => $e->getMessage()]);
            
            // Fallback to regular HTTP fetch
            try {
                $response = \Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])->timeout(15)->get($url);
                
                if ($response->successful()) {
                    $html = $response->body();
                }
            } catch (\Exception $e2) {
                // HTTP also failed
            }
        }
        
        if (!$html) {
            throw new \Exception('This website has bot protection. Please copy and paste the article text directly using the Text tab.');
        }
        
        // Extract title
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $titleMatch)) {
            $title = html_entity_decode(trim(strip_tags($titleMatch[1])));
        }
        
        // Use DOMDocument for proper HTML parsing
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($dom);
        
        // Remove unwanted elements
        $unwantedTags = ['script', 'style', 'nav', 'header', 'footer', 'aside', 'form', 'noscript', 'svg', 'iframe', 'button'];
        foreach ($unwantedTags as $tag) {
            $elements = $xpath->query('//' . $tag);
            foreach ($elements as $element) {
                if ($element->parentNode) {
                    $element->parentNode->removeChild($element);
                }
            }
        }
        
        // Remove elements with unwanted classes
        $unwantedClasses = ['sidebar', 'menu', 'navigation', 'comment', 'social', 'share', 'related', 'advertisement', 'banner', 'widget', 'popup', 'modal'];
        foreach ($unwantedClasses as $class) {
            $elements = $xpath->query("//*[contains(@class, '{$class}')]");
            foreach ($elements as $element) {
                if ($element->parentNode) {
                    $element->parentNode->removeChild($element);
                }
            }
        }
        
        // Find article content - try multiple selectors
        $articleNode = null;
        $selectors = [
            '//article',
            '//main',
            "//*[contains(@class, 'article-body')]",
            "//*[contains(@class, 'article-content')]",
            "//*[contains(@class, 'entry-content')]",
            "//*[contains(@class, 'post-content')]",
            "//*[contains(@class, 'story__content')]",
            "//*[contains(@class, 'story-body')]",
            "//*[contains(@class, 'content-body')]",
            "//div[contains(@class, 'story')]",
            "//div[contains(@class, 'content')]",
        ];
        
        foreach ($selectors as $selector) {
            $nodes = $xpath->query($selector);
            if ($nodes->length > 0) {
                $articleNode = $nodes->item(0);
                \Log::info('Found article using selector', ['selector' => $selector]);
                break;
            }
        }
        
        // If no article container found, use body
        if (!$articleNode) {
            $bodyNodes = $xpath->query('//body');
            if ($bodyNodes->length > 0) {
                $articleNode = $bodyNodes->item(0);
            }
        }
        
        // Extract all paragraphs from article
        $paragraphs = [];
        if ($articleNode) {
            $pNodes = $xpath->query('.//p', $articleNode);
            foreach ($pNodes as $pNode) {
                $text = trim($pNode->textContent);
                $text = preg_replace('/\s+/', ' ', $text);
                if (strlen($text) > 30) {
                    $paragraphs[] = $text;
                }
            }
        }
        
        \Log::info('Paragraphs extracted', ['count' => count($paragraphs)]);
        
        // If not enough paragraphs, get all text from article
        if (count($paragraphs) < 3 && $articleNode) {
            $allText = trim($articleNode->textContent);
            $allText = preg_replace('/\s+/', ' ', $allText);
            if (strlen($allText) > strlen(implode(' ', $paragraphs))) {
                $paragraphs = [$allText];
            }
        }
        
        $finalContent = implode("\n\n", $paragraphs);
        $finalContent = html_entity_decode($finalContent);
        $finalContent = preg_replace('/\s+/', ' ', $finalContent);
        $finalContent = trim($finalContent);
        
        \Log::info('Final content extracted', [
            'length' => strlen($finalContent),
            'word_count' => str_word_count($finalContent)
        ]);
        
        return [
            'title' => $title,
            'content' => $finalContent,
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

    /**
     * Export scan as PDF report
     */
    public function exportPdf(Scan $scan)
    {
        // Ensure user owns this scan
        if ($scan->user_id !== Auth::id()) {
            abort(403);
        }

        // Load relationships
        $scan->load('results');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.scan-report', [
            'scan' => $scan,
        ]);

        $filename = 'truthai-report-' . $scan->id . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

}

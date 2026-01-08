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
            'content' => 'required|string|min:50|max:100000',
            'type' => 'in:text,url',
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
            // Create the scan
            $scan = Scan::create([
                'user_id' => $user->id,
                'type' => $request->input('type', 'text'),
                'content' => $request->input('content'),
                'status' => 'pending',
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

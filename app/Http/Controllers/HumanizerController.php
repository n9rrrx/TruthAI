<?php

namespace App\Http\Controllers;

use App\Services\HumanizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HumanizerController extends Controller
{
    private HumanizerService $humanizerService;

    public function __construct(HumanizerService $humanizerService)
    {
        $this->humanizerService = $humanizerService;
    }

    /**
     * Humanize text
     */
    public function humanize(Request $request)
    {
        $request->validate([
            'content' => 'required|string|min:20',
            'level' => 'in:light,medium,strong',
            'style' => 'in:academic,casual,professional,creative',
        ]);

        $user = Auth::user();
        
        // Check daily limit
        $todayCount = $user->scans()->whereDate('created_at', today())->count();
        if ($todayCount >= 100) {
            return response()->json([
                'error' => 'Daily limit reached. Upgrade for unlimited humanizations.',
            ], 429);
        }

        try {
            $level = $request->input('level', 'medium');
            $style = $request->input('style', 'academic');
            $content = $request->input('content');

            // Create scan with humanized result
            $scan = $this->humanizerService->createScan(
                $user->id,
                $content,
                $level,
                $style
            );

            return response()->json([
                'success' => true,
                'result' => [
                    'id' => $scan->id,
                    'original' => $content,
                    'humanized' => $scan->humanized_text,
                    'word_count' => $scan->metadata['humanized_word_count'] ?? str_word_count($scan->humanized_text),
                    'level' => $level,
                    'style' => $style,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Humanization error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'error' => 'Humanization failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Regenerate humanized text
     */
    public function regenerate(Request $request)
    {
        $request->validate([
            'content' => 'required|string|min:20',
            'level' => 'in:light,medium,strong',
            'style' => 'in:academic,casual,professional,creative',
        ]);

        $level = $request->input('level', 'medium');
        $style = $request->input('style', 'academic');
        $content = $request->input('content');

        try {
            $result = $this->humanizerService->humanize($content, $level, $style);

            return response()->json([
                'success' => true,
                'result' => [
                    'humanized' => $result['humanized'],
                    'word_count' => $result['word_count'],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Regeneration failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}

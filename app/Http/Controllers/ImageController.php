<?php

namespace App\Http\Controllers;

use App\Services\ImageDetectorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Scan;

class ImageController extends Controller
{
    private ImageDetectorService $imageService;

    public function __construct(ImageDetectorService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Analyze uploaded image
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'image' => 'required_without:url|image|max:10240', // 10MB max
            'url' => 'required_without:image|url',
        ]);

        $user = Auth::user();
        
        // Check daily limit
        $todayCount = $user->scans()->whereDate('created_at', today())->count();
        if ($todayCount >= 100) {
            return response()->json([
                'error' => 'Daily scan limit reached. Upgrade for unlimited scans.',
            ], 429);
        }

        try {
            $imagePath = null;
            $imageUrl = null;

            if ($request->hasFile('image')) {
                // Store uploaded image temporarily
                $path = $request->file('image')->store('temp', 'local');
                $imagePath = Storage::disk('local')->path($path);
            } else {
                $imageUrl = $request->input('url');
                $imagePath = $imageUrl;
            }

            // Analyze the image
            $result = $this->imageService->analyzeImage($imagePath);

            // Create scan record
            $scan = Scan::create([
                'user_id' => $user->id,
                'type' => 'image',
                'content' => $imageUrl ?? 'Uploaded Image',
                'ai_score' => $result['ai_score'],
                'human_score' => $result['human_score'],
                'verdict' => $result['verdict'],
                'status' => 'completed',
                'metadata' => [
                    'ai_generated' => $result['ai_generated_probability'],
                    'deepfake' => $result['deepfake_probability'],
                ],
            ]);

            // Clean up temp file
            if ($request->hasFile('image')) {
                Storage::disk('local')->delete($path);
            }

            return response()->json([
                'success' => true,
                'result' => $result,
                'scan_id' => $scan->id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Image analysis failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Analyze video URL
     */
    public function analyzeVideo(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        // Video analysis requires premium APIs
        // For now, return a placeholder response
        return response()->json([
            'success' => false,
            'error' => 'Video analysis requires premium API access. Coming soon!',
            'note' => 'Video deepfake detection requires specialized APIs like Sensity.ai',
        ], 501);
    }
}

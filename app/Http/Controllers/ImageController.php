<?php

namespace App\Http\Controllers;

use App\Services\ImageDetectorService;
use App\Services\VideoDetectorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Scan;

class ImageController extends Controller
{
    private ImageDetectorService $imageService;
    private VideoDetectorService $videoService;

    public function __construct(ImageDetectorService $imageService, VideoDetectorService $videoService)
    {
        $this->imageService = $imageService;
        $this->videoService = $videoService;
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
     * Analyze video for deepfakes
     */
    public function analyzeVideo(Request $request)
    {
        $request->validate([
            'video' => 'required_without:url|file|mimes:mp4,avi,mov,webm|max:102400', // 100MB max
            'url' => 'required_without:video|url',
        ]);

        $user = Auth::user();
        
        // Check daily limit
        $todayCount = $user->scans()->whereDate('created_at', today())->count();
        if ($todayCount >= 50) {
            return response()->json([
                'error' => 'Daily video scan limit reached. Upgrade for more scans.',
            ], 429);
        }

        // Check if FFmpeg is available
        if (!$this->videoService->isAvailable()) {
            return response()->json([
                'error' => 'Video analysis is not available. FFmpeg is not installed.',
            ], 503);
        }

        // Increase PHP time limit for video processing
        set_time_limit(120);

        try {
            $videoPath = null;
            $videoUrl = null;

            if ($request->hasFile('video')) {
                // Store uploaded video temporarily
                $path = $request->file('video')->store('temp', 'local');
                $videoPath = Storage::disk('local')->path($path);
            } else {
                $videoUrl = $request->input('url');
                $videoPath = $videoUrl;
            }

            // Analyze the video
            $result = $this->videoService->analyzeVideo($videoPath);

            // Create scan record
            $scan = Scan::create([
                'user_id' => $user->id,
                'type' => 'video',
                'content' => $videoUrl ?? 'Uploaded Video',
                'ai_score' => $result['ai_score'],
                'human_score' => $result['human_score'],
                'verdict' => $result['verdict'],
                'status' => 'completed',
                'metadata' => [
                    'ai_generated' => $result['ai_generated_probability'],
                    'deepfake' => $result['deepfake_probability'],
                    'frames_analyzed' => $result['frames_analyzed'],
                ],
            ]);

            // Clean up temp file
            if ($request->hasFile('video')) {
                Storage::disk('local')->delete($path);
            }

            return response()->json([
                'success' => true,
                'result' => $result,
                'scan_id' => $scan->id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Video analysis failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}

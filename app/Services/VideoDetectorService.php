<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VideoDetectorService
{
    private ImageDetectorService $imageService;
    private string $ffmpegPath;
    private string $tempDir;

    public function __construct(ImageDetectorService $imageService)
    {
        $this->imageService = $imageService;
        $this->ffmpegPath = $this->findFFmpegPath();
        $this->tempDir = storage_path('app/temp/video_frames');
        
        // Ensure temp directory exists
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
    }

    /**
     * Find FFmpeg executable path
     */
    private function findFFmpegPath(): string
    {
        // Check config first
        $configPath = config('services.ffmpeg.path');
        if ($configPath && file_exists($configPath)) {
            return $configPath;
        }

        // Common Windows paths (including WinGet install)
        $possiblePaths = [
            'ffmpeg', // In PATH
            'C:\\ffmpeg\\bin\\ffmpeg.exe',
            getenv('LOCALAPPDATA') . '\\Microsoft\\WinGet\\Packages\\Gyan.FFmpeg_Microsoft.Winget.Source_8wekyb3d8bbwe\\ffmpeg-8.0.1-full_build\\bin\\ffmpeg.exe',
            getenv('LOCALAPPDATA') . '\\Microsoft\\WinGet\\Links\\ffmpeg.exe',
        ];

        // Also check for any WinGet FFmpeg version
        $wingetBase = getenv('LOCALAPPDATA') . '\\Microsoft\\WinGet\\Packages';
        if (is_dir($wingetBase)) {
            $dirs = glob($wingetBase . '\\Gyan.FFmpeg*', GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                $binPath = glob($dir . '\\ffmpeg-*\\bin\\ffmpeg.exe');
                if (!empty($binPath)) {
                    $possiblePaths[] = $binPath[0];
                }
            }
        }

        foreach ($possiblePaths as $path) {
            if ($path === 'ffmpeg') {
                // Check if in PATH by running version command
                exec('ffmpeg -version 2>&1', $output, $code);
                if ($code === 0) {
                    return 'ffmpeg';
                }
            } elseif (file_exists($path)) {
                return $path;
            }
        }

        return 'ffmpeg'; // Fallback
    }

    /**
     * Analyze video for AI/deepfake content
     */
    public function analyzeVideo(string $videoPath, int $frameInterval = 2): array
    {
        $isUrl = filter_var($videoPath, FILTER_VALIDATE_URL);
        $localPath = $videoPath;

        // If URL, download first
        if ($isUrl) {
            $localPath = $this->downloadVideo($videoPath);
        }

        try {
            // Extract frames
            $frames = $this->extractFrames($localPath, $frameInterval);
            
            if (empty($frames)) {
                throw new \Exception('Could not extract frames from video');
            }

            // Analyze each frame
            $frameResults = [];
            $totalAiScore = 0;
            $totalDeepfakeScore = 0;

            foreach ($frames as $index => $framePath) {
                try {
                    $result = $this->imageService->analyzeImage($framePath);
                    $frameResults[] = [
                        'frame' => $index + 1,
                        'timestamp' => $index * $frameInterval,
                        'ai_score' => $result['ai_generated_probability'],
                        'deepfake_score' => $result['deepfake_probability'],
                    ];
                    $totalAiScore += $result['ai_generated_probability'];
                    $totalDeepfakeScore += $result['deepfake_probability'];
                } catch (\Exception $e) {
                    Log::warning('Frame analysis failed', ['frame' => $index, 'error' => $e->getMessage()]);
                }
            }

            // Clean up frames
            $this->cleanupFrames($frames);
            if ($isUrl) {
                @unlink($localPath);
            }

            $frameCount = count($frameResults);
            if ($frameCount === 0) {
                throw new \Exception('No frames could be analyzed');
            }

            // Calculate averages
            $avgAiScore = $totalAiScore / $frameCount;
            $avgDeepfakeScore = $totalDeepfakeScore / $frameCount;
            $overallScore = max($avgAiScore, $avgDeepfakeScore);

            // Determine verdict
            $verdict = 'Likely Authentic';
            if ($overallScore >= 70) {
                $verdict = 'Likely Manipulated/AI';
            } elseif ($overallScore >= 40) {
                $verdict = 'Possibly Manipulated';
            }

            return [
                'ai_score' => round($overallScore, 2),
                'human_score' => round(100 - $overallScore, 2),
                'ai_generated_probability' => round($avgAiScore, 2),
                'deepfake_probability' => round($avgDeepfakeScore, 2),
                'verdict' => $verdict,
                'frames_analyzed' => $frameCount,
                'frame_results' => $frameResults,
            ];

        } catch (\Exception $e) {
            // Cleanup on error
            if ($isUrl && isset($localPath)) {
                @unlink($localPath);
            }
            throw $e;
        }
    }

    /**
     * Extract frames from video using FFmpeg
     */
    private function extractFrames(string $videoPath, int $interval): array
    {
        $sessionId = uniqid('video_');
        $outputPattern = $this->tempDir . DIRECTORY_SEPARATOR . "{$sessionId}_frame_%04d.jpg";

        // Use forward slashes for FFmpeg (works on Windows too)
        $ffmpegOutput = str_replace('\\', '/', $outputPattern);
        $ffmpegInput = str_replace('\\', '/', $videoPath);

        // FFmpeg command: extract 1 frame every $interval seconds
        // Using forward slashes and proper quoting for Windows
        $command = sprintf(
            '"%s" -i "%s" -vf "fps=1/%d" -q:v 2 -frames:v 10 "%s" 2>&1',
            $this->ffmpegPath,
            $ffmpegInput,
            $interval,
            $ffmpegOutput
        );

        exec($command, $output, $returnCode);

        $outputStr = implode("\n", $output);

        if ($returnCode !== 0) {
            Log::error('FFmpeg failed', ['output' => $outputStr, 'path' => $videoPath]);
            
            // Check for specific errors
            if (strpos($outputStr, 'moov atom not found') !== false) {
                throw new \Exception('Video file is corrupted or incomplete. Please try a different video file.');
            }
            if (strpos($outputStr, 'Invalid data found') !== false) {
                throw new \Exception('Invalid video format. Please upload a valid MP4, AVI, MOV, or WebM file.');
            }
            if (strpos($outputStr, 'No such file') !== false) {
                throw new \Exception('Video file not found. Please try uploading again.');
            }
            
            throw new \Exception('Failed to process video. Error: ' . substr($outputStr, 0, 200));
        }

        // Find extracted frames
        $frames = glob($this->tempDir . "/{$sessionId}_frame_*.jpg");
        sort($frames);

        return $frames;
    }

    /**
     * Download video from URL
     */
    private function downloadVideo(string $url): string
    {
        $tempFile = $this->tempDir . '/video_' . uniqid() . '.mp4';
        
        $ch = curl_init($url);
        $fp = fopen($tempFile, 'wb');
        
        curl_setopt_array($ch, [
            CURLOPT_FILE => $fp,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: video/*,*/*',
                'Accept-Language: en-US,en;q=0.9',
                'Referer: ' . parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST) . '/',
            ],
        ]);
        
        $success = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        
        curl_close($ch);
        fclose($fp);
        
        // Check if download failed
        if (!$success) {
            @unlink($tempFile);
            Log::error('Video download failed', ['url' => $url, 'error' => $error]);
            throw new \Exception('Failed to download video: ' . ($error ?: 'Connection failed'));
        }
        
        if ($httpCode !== 200) {
            @unlink($tempFile);
            if ($httpCode === 403) {
                throw new \Exception('Video URL blocked access. Try a direct video file URL (not YouTube/TikTok).');
            }
            if ($httpCode === 404) {
                throw new \Exception('Video not found at URL.');
            }
            throw new \Exception('Failed to download video (HTTP ' . $httpCode . ')');
        }
        
        // Check if we actually got a video
        $fileSize = filesize($tempFile);
        if ($fileSize < 1000) {
            @unlink($tempFile);
            throw new \Exception('Video URL did not return a valid video file. Try a direct .mp4 link.');
        }
        
        return $tempFile;
    }

    /**
     * Clean up temporary frame files
     */
    private function cleanupFrames(array $frames): void
    {
        foreach ($frames as $frame) {
            @unlink($frame);
        }
    }

    /**
     * Check if FFmpeg is available
     */
    public function isAvailable(): bool
    {
        exec($this->ffmpegPath . ' -version 2>&1', $output, $returnCode);
        return $returnCode === 0;
    }
}

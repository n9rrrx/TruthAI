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
     * Download video from URL (supports YouTube, TikTok, Instagram, etc.)
     */
    private function downloadVideo(string $url): string
    {
        $tempFile = $this->tempDir . '/video_' . uniqid() . '.mp4';
        
        // Check if URL is from a platform that needs yt-dlp
        if ($this->isPlatformUrl($url)) {
            return $this->downloadWithYtDlp($url, $tempFile);
        }
        
        // Direct download for regular URLs
        return $this->downloadDirect($url, $tempFile);
    }

    /**
     * Check if URL is from a platform that needs yt-dlp
     */
    private function isPlatformUrl(string $url): bool
    {
        $platforms = [
            'youtube.com', 'youtu.be', 'youtube-nocookie.com',
            'tiktok.com', 'vm.tiktok.com',
            'instagram.com', 'instagr.am',
            'facebook.com', 'fb.watch',
            'twitter.com', 'x.com',
            'vimeo.com',
            'dailymotion.com',
            'twitch.tv',
        ];
        
        $host = parse_url($url, PHP_URL_HOST);
        $host = preg_replace('/^www\./', '', $host ?? '');
        
        foreach ($platforms as $platform) {
            if (strpos($host, $platform) !== false || $host === $platform) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Download video using yt-dlp (for platform URLs)
     */
    private function downloadWithYtDlp(string $url, string $outputPath): string
    {
        // Find yt-dlp path
        $ytDlpPath = $this->findYtDlpPath();
        
        if (!$ytDlpPath) {
            throw new \Exception('yt-dlp is not installed. Cannot download videos from this platform.');
        }

        // Create unique prefix for finding the file later
        $prefix = 'ytdlp_' . uniqid();
        $outputTemplate = $this->tempDir . '/' . $prefix . '.%(ext)s';

        // yt-dlp command: download best quality up to 720p
        $command = sprintf(
            '"%s" -f "bestvideo[height<=720]+bestaudio/best[height<=720]/best" --merge-output-format mp4 --no-playlist -o "%s" "%s" 2>&1',
            $ytDlpPath,
            str_replace('\\', '/', $outputTemplate),
            $url
        );

        Log::info('Running yt-dlp', ['command' => $command]);
        
        exec($command, $output, $returnCode);
        $outputStr = implode("\n", $output);
        
        Log::info('yt-dlp output', ['output' => $outputStr, 'returnCode' => $returnCode]);

        if ($returnCode !== 0) {
            Log::error('yt-dlp failed', ['output' => $outputStr]);
            
            if (strpos($outputStr, 'Video unavailable') !== false) {
                throw new \Exception('Video is unavailable or private.');
            }
            if (strpos($outputStr, 'Sign in') !== false) {
                throw new \Exception('Video requires login to access.');
            }
            
            throw new \Exception('Failed to download video from platform. Error: ' . substr($outputStr, -200));
        }

        // Find the downloaded file (yt-dlp may have used a different extension)
        $downloadedFiles = glob($this->tempDir . '/' . $prefix . '.*');
        
        if (empty($downloadedFiles)) {
            throw new \Exception('Video download completed but no file was created.');
        }

        $downloadedFile = $downloadedFiles[0];
        
        if (filesize($downloadedFile) < 1000) {
            @unlink($downloadedFile);
            throw new \Exception('Downloaded video file is too small or invalid.');
        }

        return $downloadedFile;
    }

    /**
     * Find yt-dlp executable path
     */
    private function findYtDlpPath(): ?string
    {
        // Check common paths
        $possiblePaths = [
            'yt-dlp',
            getenv('LOCALAPPDATA') . '\\Microsoft\\WinGet\\Links\\yt-dlp.exe',
        ];

        // Check WinGet packages
        $wingetBase = getenv('LOCALAPPDATA') . '\\Microsoft\\WinGet\\Packages';
        if (is_dir($wingetBase)) {
            $dirs = glob($wingetBase . '\\yt-dlp*', GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                $binPath = glob($dir . '\\yt-dlp*.exe');
                if (!empty($binPath)) {
                    return $binPath[0];
                }
            }
        }

        foreach ($possiblePaths as $path) {
            if ($path === 'yt-dlp') {
                exec('yt-dlp --version 2>&1', $output, $code);
                if ($code === 0) {
                    return 'yt-dlp';
                }
            } elseif (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Direct download for regular video URLs
     */
    private function downloadDirect(string $url, string $tempFile): string
    {
        $ch = curl_init($url);
        $fp = fopen($tempFile, 'wb');
        
        curl_setopt_array($ch, [
            CURLOPT_FILE => $fp,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: video/*,*/*',
                'Accept-Language: en-US,en;q=0.9',
            ],
        ]);
        
        $success = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        fclose($fp);
        
        if (!$success) {
            @unlink($tempFile);
            throw new \Exception('Failed to download video: ' . ($error ?: 'Connection failed'));
        }
        
        if ($httpCode !== 200) {
            @unlink($tempFile);
            throw new \Exception('Failed to download video (HTTP ' . $httpCode . ')');
        }
        
        $fileSize = filesize($tempFile);
        if ($fileSize < 1000) {
            @unlink($tempFile);
            throw new \Exception('Video URL did not return a valid video file.');
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

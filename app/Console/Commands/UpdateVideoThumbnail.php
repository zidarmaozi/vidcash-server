<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UpdateVideoThumbnail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-video-thumbnail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes waiting videos to generate and save their thumbnails.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $initialTotal = $this->getRemainingVideos();
        if ($initialTotal === 0) {
            $this->info('No videos to process.');
            return;
        }

        $this->info("Starting process for {$initialTotal} videos.");

        $startTime = microtime(true); // Record start time
        $processedCount = 0;          // Initialize processed videos counter

        while (true) {
            $video = $this->getWaitingVideo();

            if (!$video) {
                $this->info('All videos have been processed.');
                break;
            }

            $this->info("Processing video: {$video->video_code} at {$this->getTimestamp()}");
            $thumbnailPath = $this->downloadThumbnail($video->video_code);

            if ($thumbnailPath) {
                $video->thumbnail_path = $thumbnailPath;
                $video->save();
                $this->info("Thumbnail saved: {$thumbnailPath} at {$this->getTimestamp()}");
            } else {
                $this->error("Failed to generate thumbnail for video: {$video->video_code} at {$this->getTimestamp()}");
            }

            $processedCount++;

            // --- Estimation Logic ---
            $elapsedTime = microtime(true) - $startTime;
            $averageTimePerVideo = $elapsedTime / $processedCount;
            $remainingVideos = $this->getRemainingVideos();
            $estimatedSecondsRemaining = $averageTimePerVideo * $remainingVideos;

            $this->info("Videos remaining: {$remainingVideos}");
            if ($remainingVideos > 0 && $processedCount > 0) {
                $this->info("Estimated time remaining: " . $this->formatSeconds($estimatedSecondsRemaining));
            }
            $this->line('--------------------------------------------------');
            // --- End of Estimation Logic ---
        }
    }

    /**
     * Formats a duration in seconds into a human-readable string.
     *
     * @param int $seconds
     * @return string
     */
    protected function formatSeconds(int $seconds): string
    {
        if ($seconds < 1) {
            return "less than a second.";
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        $parts = [];
        if ($hours > 0) {
            $parts[] = $hours . " " . ($hours > 1 ? 'hours' : 'hour');
        }
        if ($minutes > 0) {
            $parts[] = $minutes . " " . ($minutes > 1 ? 'minutes' : 'minute');
        }
        if ($secs > 0) {
            $parts[] = $secs . " " . ($secs > 1 ? 'seconds' : 'second');
        }

        return implode(', ', $parts);
    }

    protected function getTimestamp(): string
    {
        return now()->toDateTimeString();
    }

    protected function getWaitingVideo()
    {
        return Video::whereNull('thumbnail_path')
            ->where('is_active', true)
            ->inRandomOrder()
            ->first();
    }

    protected function getRemainingVideos(): int
    {
        return Video::whereNull('thumbnail_path')
            ->where('is_active', true)
            ->count();
    }

    protected function downloadThumbnail($videoCode): ?string
    {
        try {
            $url = "https://natera.smkn3singaraja.sch.id/x/$videoCode";

            $response = Http::get($url);

            if ($response->failed()) {
                return null;
            }

            $imageContents = $response->body();
            $filePath = "thumbnails/$videoCode.jpg";
            Storage::disk('public')->put($filePath, $imageContents);

            return $filePath;
        } catch (\Exception $e) {
            $this->error('Exception caught: ' . $e->getMessage());
            return null;
        }
    }
}
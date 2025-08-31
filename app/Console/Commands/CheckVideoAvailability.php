<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckVideoAvailability extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-video-availability';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check video availability on CDN and deactivate unavailable videos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting video availability check...');
        
        $videos = Video::where('is_active', true)->get();
        $totalVideos = $videos->count();
        $deactivatedCount = 0;
        
        $this->info("Found {$totalVideos} active videos to check.");
        
        $progressBar = $this->output->createProgressBar($totalVideos);
        $progressBar->start();
        
        foreach ($videos as $video) {
            try {
                if (!$this->isVideoAvailable($video->video_code)) {
                    // Video tidak tersedia, nonaktifkan
                    $video->update(['is_active' => false]);
                    $deactivatedCount++;
                    
                    $this->warn("Video {$video->video_code} ({$video->title}) is no longer available and has been deactivated.");
                    
                    // Log untuk tracking
                    Log::warning("Video {$video->video_code} ({$video->title}) deactivated due to unavailability on CDN", [
                        'video_id' => $video->id,
                        'video_code' => $video->video_code,
                        'title' => $video->title,
                        'user_id' => $video->user_id,
                        'deactivated_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                $this->error("Error checking video {$video->video_code}: " . $e->getMessage());
                
                // Log error
                Log::error("Error checking video availability", [
                    'video_id' => $video->id,
                    'video_code' => $video->video_code,
                    'error' => $e->getMessage(),
                ]);
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        
        $this->info("Video availability check completed!");
        $this->info("Total videos checked: {$totalVideos}");
        $this->info("Videos deactivated: {$deactivatedCount}");
        $this->info("Videos remaining active: " . ($totalVideos - $deactivatedCount));
        
        if ($deactivatedCount > 0) {
            $this->warn("⚠️  {$deactivatedCount} videos have been deactivated due to unavailability.");
        } else {
            $this->info("✅ All videos are available and active.");
        }
    }
    
    /**
     * Check if video is available on CDN
     * 
     * @param string $videoCode
     * @return bool
     */
    private function isVideoAvailable(string $videoCode): bool
    {
        // First try MP4 format
        $mp4Url = "https://cdn.videy.co/{$videoCode}.mp4";
        if ($this->checkUrlExists($mp4Url)) {
            return true;
        }
        
        // If MP4 not found, try MOV format
        $movUrl = "https://cdn.videy.co/{$videoCode}.mov";
        if ($this->checkUrlExists($movUrl)) {
            return true;
        }
        
        // Both formats return 404, video is not available
        return false;
    }
    
    /**
     * Check if URL exists using HEAD request
     * 
     * @param string $url
     * @return bool
     */
    private function checkUrlExists(string $url): bool
    {
        try {
            $response = Http::timeout(10)->head($url);
            
            // Consider 2xx status codes as successful
            return $response->successful();
            
        } catch (\Exception $e) {
            // Log timeout or connection errors
            Log::debug("HTTP request failed for URL: {$url}", [
                'error' => $e->getMessage(),
                'url' => $url,
            ]);
            
            // If there's a connection error, assume video might be temporarily unavailable
            // Return true to avoid false positives
            return true;
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $videos = Video::whereNull('thumbnail_path')->where('is_active', true)->get();
        foreach ($videos as $video) {
            $this->info("Processing video: {$video->video_code}");
            $thumbnailPath = $this->downloadThumbnail($video->video_code);
            if ($thumbnailPath) {
                $video->thumbnail_path = $thumbnailPath;
                $video->save();
                $this->info("Thumbnail saved: {$thumbnailPath}");
            } else {
                $this->error("Failed to generate thumbnail for video: {$video->video_code}");
            }
        }
    }

    protected function downloadThumbnail($videoCode, $videoExtension = 'mp4')
    {
        try {
            $url = "https://cdn.videy.co/$videoCode.$videoExtension";
            $durationInSeconds = FFMpeg::openUrl($url)
                                    ->getDurationInSeconds();
            $timePosition = 1;

            if ($durationInSeconds > 25) {
                $timePosition = 25;
            } elseif ($durationInSeconds > 15) {
                $timePosition = 15;
            } elseif ($durationInSeconds > 2) {
                $timePosition = 2;
            }
            
            $filePath = "thumbnails/$videoCode.jpg";
            FFMpeg::openUrl($url)
                        ->getFrameFromSeconds($timePosition)
                        ->export()
                        ->toDisk('public')
                        ->save($filePath);
            
            return $filePath;
        } catch (\Exception $e) {
            if ($videoExtension === 'mp4') {
                return $this->downloadThumbnail($videoCode, 'mov');
            } else {
                echo $e->getMessage();
                return null;
            }
        }
    }
}

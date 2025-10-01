<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
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
        while (true) {
            $video = $this->getWaitingVideo();

            if (!$video) {
                break;
            }

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

    protected function getWaitingVideo() {
        return Video::whereNull('thumbnail_path')
            ->where('is_active', true)
            ->inRandomOrder()
            ->first();
    }

    protected function downloadThumbnail($videoCode)
    {
        try {
            $url = "https://natera.smkn3singaraja.sch.id/x/$videoCode";
            
            // download image from that URL using Request Facade
            $imagePoint = \Illuminate\Support\Facades\Http::get($url);
            if ($imagePoint->failed()) {
                return null;
            }
            $imageContents = $imagePoint->body();
            // Simpan gambar ke storage/app/public/thumbnails dengan nama $videoCode.jpg
            $filePath = "thumbnails/$videoCode.jpg";
            Storage::disk('public')->put($filePath, $imageContents);
            return $filePath;
        } catch (\Exception $e) {
            return null;
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramBroadcastVideo extends Model
{
    protected $fillable = [
        'video_id',
    ];

    /**
     * Get the video that was broadcasted
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Check if a video has already been broadcasted
     * 
     * @param int $videoId
     * @return bool
     */
    public static function hasBeenBroadcasted(int $videoId): bool
    {
        return self::where('video_id', $videoId)->exists();
    }

    /**
     * Mark a video as broadcasted
     * 
     * @param int $videoId
     * @return self
     */
    public static function markAsBroadcasted(int $videoId): self
    {
        return self::create(['video_id' => $videoId]);
    }
}

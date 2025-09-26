<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VideoReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_id',
        'description',
        'reporter_ip',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the video that owns the report
     */
    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Scope for pending reports
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for reviewed reports
     */
    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    /**
     * Scope for resolved reports
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Check if IP has reported this video recently (within 6 hours)
     */
    public static function hasRecentReport($videoId, $ipAddress)
    {
        return self::where('video_id', $videoId)
            ->where('reporter_ip', $ipAddress)
            ->where('created_at', '>=', now()->subHours(6))
            ->exists();
    }
}
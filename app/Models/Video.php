<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Setting;

class Video extends Model
{
    use HasFactory; 
   protected $fillable = [
        'user_id',
        'title',
        'original_link',
        'video_code',
        'validation_level',
        'is_active',
        'thumbnail_path'
    ];

     /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    // Baris ini memberitahu Laravel untuk selalu menyertakan 'generated_link'
    protected $appends = ['generated_link', 'thumbnail_url'];

    /**
     * Fungsi ini adalah "atribut virtual" yang membuat link secara dinamis.
     */
    public function getGeneratedLinkAttribute(): string
    {
        $domain = Setting::where('key', 'video_domain')->first()?->value ?? 'videy.in';
        return 'https://' . $domain . '?id=' . $this->video_code;
    }

    /**
     * Fungsi ini adalah "atribut virtual" yang membuat link thumbnail secara dinamis.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail_path) {
            return asset('storage/' . $this->thumbnail_path);
        }
        return null;
    }

    /**
     * Scope untuk video yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk video yang tidak aktif
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Check if video is available on CDN
     * 
     * @return bool
     */
    public function isAvailableOnCdn(): bool
    {
        try {
            // First try MP4 format
            $mp4Url = "https://cdn.videy.co/{$this->video_code}.mp4";
            $mp4Response = \Illuminate\Support\Facades\Http::timeout(5)->head($mp4Url);
            if ($mp4Response->successful()) {
                return true;
            }
            
            // If MP4 not found, try MOV format
            $movUrl = "https://cdn.videy.co/{$this->video_code}.mov";
            $movResponse = \Illuminate\Support\Facades\Http::timeout(5)->head($movUrl);
            if ($movResponse->successful()) {
                return true;
            }
            
            // Both formats return 404, video is not available
            return false;
            
        } catch (\Exception $e) {
            // If there's a connection error, assume video might be temporarily unavailable
            // Return true to avoid false positives
            return true;
        }
    }

    /**
     * Get CDN URLs for this video
     * 
     * @return array
     */
    public function getCdnUrls(): array
    {
        return [
            'mp4' => "https://cdn.videy.co/{$this->video_code}.mp4",
            'mov' => "https://cdn.videy.co/{$this->video_code}.mov",
        ];
    }

    
// fitur untuk views
public function views()
{
    return $this->hasMany(View::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}

public function reports()
{
    return $this->hasMany(VideoReport::class);
}

public function pendingReports()
{
    return $this->hasMany(VideoReport::class)->where('status', 'pending');
}

}

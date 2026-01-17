<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;

class Folder extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function getPublicLinkAttribute()
    {
        $domain = Setting::where('key', 'folder_domain')->value('value');

        if ($domain) {
            return "https://{$domain}/{$this->slug}";
        }

        return url('/f/' . $this->slug);
    }
}

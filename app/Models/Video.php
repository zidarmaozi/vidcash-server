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
    ];

     /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    // Baris ini memberitahu Laravel untuk selalu menyertakan 'generated_link'
    protected $appends = ['generated_link'];

    /**
     * Fungsi ini adalah "atribut virtual" yang membuat link secara dinamis.
     */
    public function getGeneratedLinkAttribute(): string
    {
        $domain = Setting::where('key', 'video_domain')->first()?->value ?? 'videy.in';
        return 'https://' . $domain . '?id=' . $this->video_code;
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

    
// fitur untuk views
public function views()
{
    return $this->hasMany(View::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}

}

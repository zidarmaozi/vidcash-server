<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $fillable = [
    'video_id',
    'ip_address',
];

    use HasFactory; 
    //
    public function user()
{
    return $this->belongsTo(User::class);
}
public function video()
{
    return $this->belongsTo(Video::class);
}
}

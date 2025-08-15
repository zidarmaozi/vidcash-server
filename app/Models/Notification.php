<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
    'user_id',
    'type',
    'message',
    'read_at',
];
}

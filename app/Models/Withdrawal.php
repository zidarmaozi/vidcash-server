<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'payment_info',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // --- TAMBAHKAN FUNGSI INI ---
    public function getFormattedIdAttribute(): string
    {
        // Menambahkan '#' di depan dan membuat panjangnya minimal 5 digit
        return '#' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }
}
<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $fillable = [
        'video_id',
        'ip_address',
        'income_amount',
        'cpm_at_time',
        'validation_passed',
        'income_generated',
    ];

    protected $casts = [
        'income_amount' => 'decimal:2',
        'cpm_at_time' => 'decimal:2',
        'validation_passed' => 'boolean',
        'income_generated' => 'boolean',
    ];

    use HasFactory; 

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * Get the total income generated from this view
     */
    public function getTotalIncomeAttribute(): float
    {
        return $this->income_generated ? $this->income_amount : 0.00;
    }

    /**
     * Scope to get only views that generated income
     */
    public function scopeIncomeGenerated($query)
    {
        return $query->where('income_generated', true);
    }

    /**
     * Scope to get only views that passed validation
     */
    public function scopeValidationPassed($query)
    {
        return $query->where('validation_passed', true);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'content',
        'title',
        'ai_score',
        'human_score',
        'status',
        'humanized_text',
        'word_count',
        'metadata',
    ];

    protected $casts = [
        'ai_score' => 'decimal:2',
        'human_score' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the scan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all results from different AI detectors
     */
    public function results(): HasMany
    {
        return $this->hasMany(ScanResult::class);
    }

    /**
     * Scope for completed scans
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for detection scans (not humanization)
     */
    public function scopeDetections($query)
    {
        return $query->where('type', '!=', 'humanize');
    }

    /**
     * Scope for humanization scans
     */
    public function scopeHumanizations($query)
    {
        return $query->where('type', 'humanize');
    }

    /**
     * Get detection verdict based on AI score
     */
    public function getVerdictAttribute(): string
    {
        if ($this->ai_score === null) return 'pending';
        if ($this->ai_score >= 80) return 'ai_generated';
        if ($this->ai_score >= 50) return 'mixed';
        if ($this->ai_score >= 20) return 'likely_human';
        return 'human';
    }

    /**
     * Auto-generate title from content
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($scan) {
            if (empty($scan->title)) {
                $scan->title = \Str::limit(strip_tags($scan->content), 50);
            }
            $scan->word_count = str_word_count(strip_tags($scan->content));
        });
    }
}

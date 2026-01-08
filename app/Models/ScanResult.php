<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_id',
        'provider',
        'ai_score',
        'human_score',
        'confidence',
        'raw_response',
        'status',
        'error_message',
    ];

    protected $casts = [
        'ai_score' => 'decimal:2',
        'human_score' => 'decimal:2',
        'confidence' => 'decimal:2',
        'raw_response' => 'array',
    ];

    /**
     * Get the scan this result belongs to
     */
    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }

    /**
     * Provider display names
     */
    public function getProviderNameAttribute(): string
    {
        return match($this->provider) {
            'gptzero' => 'GPTZero',
            'originality' => 'Originality.ai',
            'copyleaks' => 'Copyleaks',
            'sapling' => 'Sapling AI',
            'writer' => 'Writer.com',
            default => ucfirst($this->provider),
        };
    }
}

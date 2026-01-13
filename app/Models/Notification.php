<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'link',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark the notification as read
     */
    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Check if notification is unread
     */
    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Create a scan notification
     */
    public static function createScanNotification(User $user, $scan, string $message): self
    {
        return self::create([
            'user_id' => $user->id,
            'type' => 'scan',
            'title' => 'Scan Complete',
            'message' => $message,
            'icon' => '🔍',
            'link' => '/dashboard/scan/' . $scan->id,
        ]);
    }

    /**
     * Create an account notification
     */
    public static function createAccountNotification(User $user, string $title, string $message): self
    {
        return self::create([
            'user_id' => $user->id,
            'type' => 'account',
            'title' => $title,
            'message' => $message,
            'icon' => '👤',
        ]);
    }

    /**
     * Create a system notification
     */
    public static function createSystemNotification(User $user, string $title, string $message): self
    {
        return self::create([
            'user_id' => $user->id,
            'type' => 'system',
            'title' => $title,
            'message' => $message,
            'icon' => '🔔',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get notifications for the current user (API)
     */
    public function index(Request $request)
    {
        $notifications = Auth::user()
            ->notifications()
            ->take(20)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'link' => $notification->link,
                    'is_read' => !$notification->isUnread(),
                    'time_ago' => $notification->created_at->diffForHumans(),
                    'created_at' => $notification->created_at->format('M d, Y H:i'),
                ];
            });

        $unreadCount = Auth::user()->unread_notifications_count;

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Notification $notification)
    {
        // Ensure user owns the notification
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()->unread()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification
     */
    public function destroy(Notification $notification)
    {
        // Ensure user owns the notification
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        Auth::user()->notifications()->delete();

        return response()->json(['success' => true]);
    }
}

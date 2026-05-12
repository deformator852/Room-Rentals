<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->limit(30)
            ->get()
            ->map(function (Notification $notification) {
                $metadata = $notification->metadata ?? [];
                $actionUrl = $metadata['action_url'] ?? null;

                if (! $actionUrl && $notification->event_type === 'booking_requested') {
                    $actionUrl = route('owner.booking-requests');
                }

                if (! $actionUrl && $notification->event_type === 'booking_cancellation_requested') {
                    $actionUrl = route('profile.my-rentals');
                }

                if (! $actionUrl && $notification->event_type === 'booking_rated') {
                    $actionUrl = route('owner.booking-requests');
                }

                return [
                    'id' => $notification->id,
                    'event_type' => $notification->event_type,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at?->format('d.m.Y H:i'),
                    'metadata' => $metadata,
                    'action_url' => $actionUrl,
                ];
            });

        $unreadCount = Notification::query()
            ->where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        Notification::query()
            ->where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, Notification $notification): JsonResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->delete();

        return response()->json(['ok' => true]);
    }

    public function clear(Request $request): JsonResponse
    {
        Notification::query()
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['ok' => true]);
    }
}

<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public Notification $notification)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('App.Models.User.'.$this->notification->user_id)];
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'event_type' => $this->notification->event_type,
            'message' => $this->notification->message,
            'created_at' => $this->notification->created_at?->format('d.m.Y H:i'),
            'metadata' => $this->notification->metadata ?? [],
        ];
    }
}

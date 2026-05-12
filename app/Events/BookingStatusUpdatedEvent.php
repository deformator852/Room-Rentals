<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingStatusUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('App.Models.User.'.$this->booking->tenant_id)];
    }

    public function broadcastAs(): string
    {
        return 'booking.status-updated';
    }

    public function broadcastWith(): array
    {
        $property = $this->booking->property;
        $statusLabel = match ($this->booking->status) {
            'confirmed' => 'підтверджено',
            'rejected' => 'відхилено',
            'cancelled' => 'скасовано за згодою сторін',
            'check_out' => 'завершено',
            default => $this->booking->status,
        };

        return [
            'booking_id' => $this->booking->id,
            'property_id' => $property->id,
            'property_title' => $property->title,
            'status' => $this->booking->status,
            'check_in' => $this->booking->check_in->toDateString(),
            'check_out' => $this->booking->check_out->toDateString(),
            'message' => "Вашу заявку {$statusLabel}: {$property->title}",
        ];
    }
}

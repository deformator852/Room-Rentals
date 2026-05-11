<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingRequestedEvent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('App.Models.User.'.$this->booking->property->user_id)];
    }

    public function broadcastAs(): string
    {
        return 'booking.requested';
    }

    public function broadcastWith(): array
    {
        $tenant = $this->booking->tenant;
        $property = $this->booking->property;

        return [
            'booking_id' => $this->booking->id,
            'property_id' => $property->id,
            'property_title' => $property->title,
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'check_in' => $this->booking->check_in->toDateString(),
            'check_out' => $this->booking->check_out->toDateString(),
            'message' => "Нова заявка на оренду: {$property->title}",
        ];
    }
}

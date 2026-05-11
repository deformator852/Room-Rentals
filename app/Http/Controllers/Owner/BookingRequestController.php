<?php

namespace App\Http\Controllers\Owner;

use App\Events\BookingStatusUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingRequestController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::query()
            ->whereHas('property', fn ($query) => $query->where('user_id', $request->user()->id))
            ->with(['tenant', 'property'])
            ->orderByRaw("case when status = 'pending' then 0 else 1 end")
            ->latest()
            ->paginate(20);

        return view('pages.owner.booking-requests', [
            'bookings' => $bookings,
        ]);
    }

    public function approve(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->property->user_id === $request->user()->id, 403);

        $status = DB::transaction(function () use ($booking) {
            $lockedBooking = Booking::query()
                ->whereKey($booking->id)
                ->lockForUpdate()
                ->with(['property', 'tenant'])
                ->firstOrFail();

            if ($lockedBooking->status !== 'pending') {
                return 'not_pending';
            }

            $hasConfirmedOverlap = Booking::query()
                ->where('property_id', $lockedBooking->property_id)
                ->where('id', '!=', $lockedBooking->id)
                ->where('status', 'confirmed')
                ->where('check_in', '<', $lockedBooking->check_out->toDateString())
                ->where('check_out', '>', $lockedBooking->check_in->toDateString())
                ->exists();

            if ($hasConfirmedOverlap) {
                $lockedBooking->update(['status' => 'rejected']);
                Notification::query()->create([
                    'user_id' => $lockedBooking->tenant_id,
                    'event_type' => 'booking_rejected',
                    'message' => "Вашу заявку відхилено: {$lockedBooking->property->title}",
                    'metadata' => [
                        'booking_id' => $lockedBooking->id,
                        'property_id' => $lockedBooking->property_id,
                    ],
                    'is_read' => false,
                ]);
                broadcast(new BookingStatusUpdatedEvent($lockedBooking));

                return 'already_booked';
            }

            $lockedBooking->update(['status' => 'confirmed']);
            Notification::query()->create([
                'user_id' => $lockedBooking->tenant_id,
                'event_type' => 'booking_confirmed',
                'message' => "Вашу заявку підтверджено: {$lockedBooking->property->title}",
                'metadata' => [
                    'booking_id' => $lockedBooking->id,
                    'property_id' => $lockedBooking->property_id,
                ],
                'is_read' => false,
            ]);
            broadcast(new BookingStatusUpdatedEvent($lockedBooking));

            $conflictingPending = Booking::query()
                ->where('property_id', $lockedBooking->property_id)
                ->where('id', '!=', $lockedBooking->id)
                ->where('status', 'pending')
                ->where('check_in', '<', $lockedBooking->check_out->toDateString())
                ->where('check_out', '>', $lockedBooking->check_in->toDateString())
                ->with(['property', 'tenant'])
                ->lockForUpdate()
                ->get();

            foreach ($conflictingPending as $conflictBooking) {
                $conflictBooking->update(['status' => 'rejected']);
                Notification::query()->create([
                    'user_id' => $conflictBooking->tenant_id,
                    'event_type' => 'booking_rejected',
                    'message' => "Вашу заявку відхилено: {$conflictBooking->property->title}",
                    'metadata' => [
                        'booking_id' => $conflictBooking->id,
                        'property_id' => $conflictBooking->property_id,
                    ],
                    'is_read' => false,
                ]);
                broadcast(new BookingStatusUpdatedEvent($conflictBooking));
            }

            return 'approved';
        });

        return match ($status) {
            'not_pending' => back()->with('status', 'Ця заявка вже була оброблена.'),
            'already_booked' => back()->with('status', 'Конфлікт із вже підтвердженим бронюванням. Заявку відхилено.'),
            default => back()->with('status', 'Заявку підтверджено. Конфліктні заявки автоматично відхилені.'),
        };
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->property->user_id === $request->user()->id, 403);

        if ($booking->status !== 'pending') {
            return back()->with('status', 'Ця заявка вже була оброблена.');
        }

        $booking->loadMissing(['property', 'tenant']);
        $booking->update(['status' => 'rejected']);
        Notification::query()->create([
            'user_id' => $booking->tenant_id,
            'event_type' => 'booking_rejected',
            'message' => "Вашу заявку відхилено: {$booking->property->title}",
            'metadata' => [
                'booking_id' => $booking->id,
                'property_id' => $booking->property_id,
            ],
            'is_read' => false,
        ]);
        broadcast(new BookingStatusUpdatedEvent($booking));

        return back()->with('status', 'Заявку відхилено.');
    }

    public function cancelConfirmed(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->property->user_id === $request->user()->id, 403);

        if ($booking->status !== 'confirmed') {
            return back()->with('status', 'Скасувати можна лише підтверджене бронювання.');
        }

        $booking->loadMissing(['property', 'tenant']);
        $booking->update(['status' => 'cancelled']);

        Notification::query()->create([
            'user_id' => $booking->tenant_id,
            'event_type' => 'booking_cancelled',
            'message' => "Власник скасував підтверджене бронювання: {$booking->property->title}",
            'metadata' => [
                'booking_id' => $booking->id,
                'property_id' => $booking->property_id,
            ],
            'is_read' => false,
        ]);

        broadcast(new BookingStatusUpdatedEvent($booking));

        return back()->with('status', 'Підтверджене бронювання скасовано.');
    }
}

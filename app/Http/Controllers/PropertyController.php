<?php

namespace App\Http\Controllers;

use App\Events\BookingRequestedEvent;
use App\Enums\PropertyStatus;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PropertyController extends Controller
{
    public function index()
    {
        //
    }

    public function myProperties()
    {
        return view('pages.property.my-properties');
    }

    public function create()
    {
        return view('pages.property.create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        $property = Property::query()
            ->with(['owner', 'photos', 'reviews.tenant', 'settlement', 'bookings'])
            ->where('status', PropertyStatus::Published)
            ->findOrFail($id);

        return view('pages.property.show', [
            'property' => $property,
        ]);
    }

    public function book(Request $request, Property $property)
    {
        abort_unless($property->isPublished(), 404);

        if ($property->user_id === auth()->id()) {
            throw ValidationException::withMessages([
                'check_in' => 'Ви не можете бронювати власне оголошення.',
            ]);
        }
        $maxBookingDate = now()->addMonth()->toDateString();

        $validated = $request->validate([
            'check_in' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.$maxBookingDate],
            'check_out' => ['required', 'date', 'after:check_in', 'before_or_equal:'.$maxBookingDate],
            'comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'check_in.required' => 'Оберіть дату заїзду.',
            'check_in.after_or_equal' => 'Дата заїзду не може бути в минулому.',
            'check_in.before_or_equal' => 'Дата заїзду має бути не пізніше ніж через 1 місяць від сьогодні.',
            'check_out.required' => 'Оберіть дату виїзду.',
            'check_out.after' => 'Дата виїзду має бути пізніше дати заїзду.',
            'check_out.before_or_equal' => 'Дата виїзду має бути не пізніше ніж через 1 місяць від сьогодні.',
            'comment.max' => 'Коментар не може перевищувати 1000 символів.',
        ]);

        $checkIn = Carbon::parse($validated['check_in'])->startOfDay();
        $checkOut = Carbon::parse($validated['check_out'])->startOfDay();
        $nights = $checkIn->diffInDays($checkOut);

        if ($nights < $property->min_nights) {
            throw ValidationException::withMessages([
                'check_out' => "Мінімальна тривалість бронювання: {$property->min_nights} ночей.",
            ]);
        }

        $hasOverlap = Booking::query()
            ->where('property_id', $property->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in', '<', $checkOut->toDateString())
            ->where('check_out', '>', $checkIn->toDateString())
            ->exists();

        if ($hasOverlap) {
            throw ValidationException::withMessages([
                'check_in' => 'Ці дати вже зайняті. Оберіть інший період.',
            ]);
        }

        $booking = Booking::query()->create([
            'property_id' => $property->id,
            'tenant_id' => auth()->id(),
            'check_in' => $checkIn->toDateString(),
            'check_out' => $checkOut->toDateString(),
            'nights_count' => $nights,
            'total_price' => $property->price_per_night * $nights,
            'status' => 'pending',
            'comment' => $validated['comment'] ?? null,
        ]);

        $booking->load(['tenant', 'property']);
        Notification::query()->create([
            'user_id' => $property->user_id,
            'event_type' => 'booking_requested',
            'message' => "Нова заявка на оренду: {$property->title}",
            'metadata' => [
                'booking_id' => $booking->id,
                'property_id' => $property->id,
                'check_in' => $booking->check_in->toDateString(),
                'check_out' => $booking->check_out->toDateString(),
                'tenant_name' => $booking->tenant->name,
                'action_url' => route('owner.booking-requests'),
            ],
            'is_read' => false,
        ]);
        broadcast(new BookingRequestedEvent($booking));

        return redirect()
            ->route('property.show', $property)
            ->with('status', 'Бронювання створено. Очікуйте підтвердження від власника.');
    }

    public function edit(Property $property)
    {
        abort_unless($property->user_id === auth()->id(), 403);

        return view('pages.property.edit', [
            'property' => $property,
        ]);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}

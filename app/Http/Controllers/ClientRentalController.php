<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientRentalController extends Controller
{
    public function index(Request $request)
    {
        Booking::syncExpiredConfirmedToCheckout();

        $bookings = Booking::query()
            ->where('tenant_id', $request->user()->id)
            ->whereIn('status', ['pending', 'confirmed', 'cancelled', 'check_out', 'rejected'])
            ->with(['property.photos', 'review'])
            ->latest()
            ->get();

        $today = now()->startOfDay();

        $activeRentals = $bookings->filter(
            fn (Booking $booking) => $booking->status === 'confirmed'
                && $booking->check_in->startOfDay()->lte($today)
                && $booking->check_out->startOfDay()->gt($today)
        )->values();

        $upcomingRentals = $bookings->filter(
            fn (Booking $booking) => $booking->status === 'confirmed'
                && $booking->check_in->startOfDay()->gt($today)
        )->values();

        $pendingRequests = $bookings->filter(fn (Booking $booking) => $booking->status === 'pending')->values();
        $reviewRequiredRentals = $bookings->filter(
            fn (Booking $booking) => in_array($booking->status, ['confirmed', 'cancelled', 'check_out'], true)
                && $booking->check_out->startOfDay()->lte($today)
                && ! $booking->review
        )->values();
        $rentalHistory = $bookings->filter(
            fn (Booking $booking) => $booking->check_out->startOfDay()->lte($today)
                || in_array($booking->status, ['cancelled', 'rejected', 'check_out'], true)
        )->values();

        return view('pages.profile.my-rentals', [
            'activeRentals' => $activeRentals,
            'upcomingRentals' => $upcomingRentals,
            'pendingRequests' => $pendingRequests,
            'reviewRequiredRentals' => $reviewRequiredRentals,
            'rentalHistory' => $rentalHistory,
        ]);
    }

    public function requestCancellation(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->tenant_id === $request->user()->id, 403);
        Booking::syncExpiredConfirmedToCheckout();
        $booking->refresh();

        if ($booking->status !== 'confirmed') {
            return back()->with('status', 'Скасування можливе лише для підтвердженої оренди.');
        }

        if ($booking->check_out->startOfDay()->lte(now()->startOfDay())) {
            return back()->with('status', 'Якщо дата виїзду сьогодні або в минулому, скасування недоступне.');
        }

        if ($booking->cancellation_requested_by_tenant_at) {
            return back()->with('status', 'Ви вже підтвердили скасування. Очікуємо підтвердження орендодавця.');
        }

        $booking->loadMissing('property');
        $booking->update(['cancellation_requested_by_tenant_at' => now()]);

        if (! $booking->cancellation_requested_by_owner_at) {
            Notification::query()->create([
                'user_id' => $booking->property->user_id,
                'event_type' => 'booking_cancellation_requested',
                'message' => "Орендар просить скасувати підтверджену оренду: {$booking->property->title}",
                'metadata' => [
                    'booking_id' => $booking->id,
                    'property_id' => $booking->property_id,
                    'action_url' => route('owner.booking-requests'),
                ],
                'is_read' => false,
            ]);
        }

        if ($booking->cancellation_requested_by_owner_at) {
            $booking->update(['status' => 'cancelled']);

            Notification::query()->create([
                'user_id' => $booking->tenant_id,
                'event_type' => 'booking_cancelled',
                'message' => "Бронювання скасовано за згодою сторін: {$booking->property->title}",
                'metadata' => [
                    'booking_id' => $booking->id,
                    'property_id' => $booking->property_id,
                ],
                'is_read' => false,
            ]);

            Notification::query()->create([
                'user_id' => $booking->property->user_id,
                'event_type' => 'booking_cancelled',
                'message' => "Бронювання скасовано за згодою сторін: {$booking->property->title}",
                'metadata' => [
                    'booking_id' => $booking->id,
                    'property_id' => $booking->property_id,
                ],
                'is_read' => false,
            ]);

            broadcast(new \App\Events\BookingStatusUpdatedEvent($booking));

            return back()->with('status', 'Скасування підтверджено обома сторонами.');
        }

        return back()->with('status', 'Ваш запит на скасування надіслано орендодавцю.');
    }

    public function submitReview(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->tenant_id === $request->user()->id, 403);
        Booking::syncExpiredConfirmedToCheckout();
        $booking->refresh();

        $today = now()->startOfDay();

        if ($booking->check_out->startOfDay()->gt($today)) {
            return back()->with('status', 'Оцінку можна залишити лише після дати виїзду.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ], [
            'rating.required' => 'Оберіть оцінку від 1 до 5.',
            'rating.min' => 'Оцінка має бути від 1 до 5.',
            'rating.max' => 'Оцінка має бути від 1 до 5.',
        ]);

        DB::transaction(function () use ($booking, $validated) {
            $booking->loadMissing('property');

            Review::query()->updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'tenant_id' => $booking->tenant_id,
                    'property_id' => $booking->property_id,
                    'rating' => (int) $validated['rating'],
                ]
            );

            $stats = Review::query()
                ->where('property_id', $booking->property_id)
                ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as reviews_count')
                ->first();

            $booking->property->update([
                'avg_rating' => (float) ($stats?->avg_rating ?? 0),
                'reviews_count' => (int) ($stats?->reviews_count ?? 0),
            ]);

            Notification::query()->create([
                'user_id' => $booking->property->user_id,
                'event_type' => 'booking_rated',
                'message' => "Орендар поставив оцінку {$validated['rating']}/5: {$booking->property->title}",
                'metadata' => [
                    'booking_id' => $booking->id,
                    'property_id' => $booking->property_id,
                    'rating' => (int) $validated['rating'],
                    'action_url' => route('owner.booking-requests'),
                ],
                'is_read' => false,
            ]);
        });

        return back()->with('status', 'Дякуємо! Оцінку збережено.');
    }
}

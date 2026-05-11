<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class ClientRentalController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::query()
            ->where('tenant_id', $request->user()->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['property.photos'])
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

        return view('pages.profile.my-rentals', [
            'activeRentals' => $activeRentals,
            'upcomingRentals' => $upcomingRentals,
            'pendingRequests' => $pendingRequests,
        ]);
    }
}

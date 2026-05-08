<?php

namespace App\Http\Controllers;

use App\Enums\PropertyStatus;
use App\Models\Booking;
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
            ->with(['owner', 'photos', 'reviews.tenant', 'settlement'])
            ->where('status', PropertyStatus::Published)
            ->findOrFail($id);

        return view('pages.property.show', [
            'property' => $property,
        ]);
    }

    public function book(Request $request, Property $property)
    {
        abort_unless($property->isPublished(), 404);

        $validated = $request->validate([
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
        ], [
            'check_in.required' => 'Оберіть дату заїзду.',
            'check_in.after_or_equal' => 'Дата заїзду не може бути в минулому.',
            'check_out.required' => 'Оберіть дату виїзду.',
            'check_out.after' => 'Дата виїзду має бути пізніше дати заїзду.',
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

        Booking::query()->create([
            'property_id' => $property->id,
            'tenant_id' => auth()->id(),
            'check_in' => $checkIn->toDateString(),
            'check_out' => $checkOut->toDateString(),
            'nights_count' => $nights,
            'total_price' => $property->price_per_night * $nights,
            'status' => 'pending',
        ]);

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

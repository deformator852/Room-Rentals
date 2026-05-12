<?php

namespace App\Livewire\Property;

use App\Enums\PropertyStatus;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;


class MyProperties extends Component
{
    use WithPagination;

    public function suspend(string $propertyId): void
    {
        $property = Property::query()
            ->whereKey($propertyId)
            ->where('user_id', Auth::id())
            ->first();

        if (! $property) {
            return;
        }

        if ($property->status !== PropertyStatus::Published) {
            return;
        }

        $property->update([
            'status' => PropertyStatus::Inactive,
        ]);

        session()->flash('status', 'Оголошення призупинено.');
    }

    public function resume(string $propertyId): void
    {
        $property = Property::query()
            ->whereKey($propertyId)
            ->where('user_id', Auth::id())
            ->first();

        if (! $property) {
            return;
        }

        if ($property->status !== PropertyStatus::Inactive) {
            return;
        }

        $property->update([
            'status' => PropertyStatus::Published,
        ]);

        session()->flash('status', 'Оголошення знову активне.');
    }

    public function render(): View
    {
        $properties = Auth::user()
            ->properties()
            ->with(['mainPhoto', 'settlement'])
            ->latest()
            ->paginate(12);

        return view('livewire.property.my-properties', [
            'properties' => $properties,
        ]);
    }
}

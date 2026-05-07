<?php

namespace App\Livewire\Property;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;


class MyProperties extends Component
{
    use WithPagination;

    public function render(): View
    {
        $properties = Auth::user()
            ->properties()
            ->with('mainPhoto')
            ->latest()
            ->paginate(12);

        return view('livewire.property.my-properties', [
            'properties' => $properties,
        ]);
    }
}

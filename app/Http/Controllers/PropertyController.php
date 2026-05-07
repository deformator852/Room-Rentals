<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

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
        //
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

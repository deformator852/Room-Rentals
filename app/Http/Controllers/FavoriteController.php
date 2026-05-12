<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = Favorite::query()
            ->where('user_id', $request->user()->id)
            ->with(['property.mainPhoto', 'property.photos', 'property.settlement'])
            ->latest()
            ->paginate(12);

        return view('pages.profile.favorites', [
            'favorites' => $favorites,
        ]);
    }

    public function store(Request $request, Property $property): RedirectResponse
    {
        Favorite::query()->firstOrCreate([
            'user_id' => $request->user()->id,
            'property_id' => $property->id,
        ]);

        return back()->with('status', 'Додано в обране.');
    }

    public function destroy(Request $request, Property $property): RedirectResponse
    {
        Favorite::query()
            ->where('user_id', $request->user()->id)
            ->where('property_id', $property->id)
            ->delete();

        return back()->with('status', 'Видалено з обраного.');
    }
}

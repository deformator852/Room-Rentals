@extends('layouts.app')

@section('main')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Обране</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">Збережені об'єкти.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400">
                {{ session('status') }}
            </div>
        @endif

        @if($favorites->isEmpty())
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 text-sm text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                Ви ще не додали жодного об'єкта в обране.
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($favorites as $favorite)
                    @php
                        $property = $favorite->property;
                        $cover = $property?->mainPhoto?->first()?->url ?? $property?->photos?->sortBy('position')?->first()?->url;
                        $coverUrl = $cover
                            ? (\Illuminate\Support\Str::startsWith($cover, ['http://', 'https://']) ? $cover : Storage::url($cover))
                            : null;
                    @endphp

                    @if($property)
                        <article class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <a href="{{ route('property.show', $property) }}" class="block">
                                <div class="relative aspect-[4/3] bg-zinc-100 dark:bg-zinc-800">
                                    @if ($coverUrl)
                                        <img src="{{ $coverUrl }}" alt="{{ $property->title }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full items-center justify-center text-sm text-zinc-500">Немає фото</div>
                                    @endif
                                </div>
                            </a>

                            <div class="p-4">
                                <a href="{{ route('property.show', $property) }}" class="line-clamp-1 text-lg font-semibold text-zinc-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400">
                                    {{ $property->title }}
                                </a>

                                <p class="mt-1 line-clamp-1 text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $property->settlement?->name ?? '—' }}@if($property->settlement?->region), {{ $property->settlement->region }}@endif
                                </p>

                                <div class="mt-4 flex items-center justify-between">
                                    <div class="text-xl font-semibold text-zinc-900 dark:text-white">
                                        {{ number_format((float) $property->price_per_night, 0, '.', ' ') }} ₴
                                    </div>
                                    <form method="POST" action="{{ route('favorites.destroy', $property) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-zinc-200 px-3 py-2 text-sm font-medium text-zinc-800 hover:bg-zinc-300 dark:bg-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-600">
                                            Видалити
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endif
                @endforeach
            </div>

            <div class="mt-8">
                {{ $favorites->links() }}
            </div>
        @endif
    </section>
@endsection

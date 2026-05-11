@extends('layouts.app')

@section('main')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Мої оренди</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">Ваші поточні та майбутні оренди.</p>
        </div>

        @php
            $sections = [
                'Зараз орендую' => $activeRentals,
                'Заплановані оренди' => $upcomingRentals,
                'Мої заявки в очікуванні' => $pendingRequests,
            ];
        @endphp

        @forelse($sections as $title => $items)
            <div class="mb-8">
                <h2 class="mb-3 text-lg font-semibold text-zinc-900 dark:text-white">{{ $title }}</h2>

                @if($items->isEmpty())
                    <div class="rounded-2xl border border-zinc-200 bg-white p-5 text-sm text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                        Немає записів у цьому розділі.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($items as $booking)
                            @php
                                $cover = $booking->property->photos->sortBy('position')->first()?->url;
                                $coverUrl = $cover
                                    ? (\Illuminate\Support\Str::startsWith($cover, ['http://', 'https://']) ? $cover : Storage::url($cover))
                                    : null;
                            @endphp

                            <article class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                                <div class="grid gap-4 p-4 md:grid-cols-[220px_1fr]">
                                    <a href="{{ route('property.show', $booking->property) }}" class="block overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800">
                                        @if($coverUrl)
                                            <img src="{{ $coverUrl }}" alt="{{ $booking->property->title }}" class="h-36 w-full object-cover md:h-full">
                                        @else
                                            <div class="flex h-36 items-center justify-center text-sm text-zinc-500 md:h-full">Немає фото</div>
                                        @endif
                                    </a>

                                    <div>
                                        <a href="{{ route('property.show', $booking->property) }}" class="text-lg font-medium text-zinc-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400">
                                            {{ $booking->property->title }}
                                        </a>
                                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                                            {{ $booking->check_in->format('d.m.Y') }} - {{ $booking->check_out->format('d.m.Y') }} ({{ $booking->nights_count }} ночей)
                                        </p>
                                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                                            Сума: {{ number_format((float) $booking->total_price, 0, '.', ' ') }} ₴
                                        </p>
                                        @if($booking->comment)
                                            <p class="mt-3 whitespace-pre-line text-sm text-zinc-700 dark:text-zinc-200">{{ $booking->comment }}</p>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
        @endforelse
    </section>
@endsection

@php use Illuminate\Support\Str; @endphp
@php use Illuminate\Support\Facades\Storage; @endphp
@php use Carbon\Carbon; @endphp
@extends('layouts.app')

@section('main')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div
                class="mb-6 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400">
                {{ session('status') }}
            </div>
        @endif

        <div class="mb-6">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $property->settlement?->name ?? '—' }}@if($property->settlement?->region)
                    , {{ $property->settlement->region }}
                @endif, {{ $property->address }}</p>
            <h1 class="mt-1 text-3xl font-semibold text-zinc-900 dark:text-white">{{ $property->title }}</h1>
            <div class="mt-2 flex flex-wrap items-center gap-4 text-sm text-zinc-600 dark:text-zinc-300">
                <span>{{ $property->property_type->label() }}</span>
                <span>{{ $property->rooms_count }} кімн.</span>
                <span>{{ number_format($property->area, 0) }} м²</span>
                <span>⭐ {{ number_format($property->avg_rating, 1) }} ({{ $property->reviews_count }} відгуків)</span>
            </div>
        </div>

        @php
            $photos = $property->photos
                ->sortBy('position')
                ->values()
                ->map(function ($photo) {
                    $photo->resolved_url = Str::startsWith($photo->url, ['http://', 'https://'])
                        ? $photo->url
                        : Storage::url($photo->url);

                    return $photo;
                });
            $primaryPhoto = $photos->first();
            $mapQuery = urlencode(($property->settlement?->name ?? '') . ', ' . ($property->settlement?->region ?? '') . ', ' . $property->address);
            $checkInDefault = now()->addDay()->toDateString();
            $checkOutDefault = now()->addDays(max(2, $property->min_nights))->toDateString();
            $maxBookingDate = now()->addMonth()->toDateString();
            $activeConfirmedBooking = $property->bookings
                ->where('status', 'confirmed')
                ->first(fn($booking) => $booking->check_in->lte(now()) && $booking->check_out->gt(now()));
            $unavailableRanges = $property->bookings
                ->whereIn('status', ['pending', 'confirmed'])
                ->map(fn($booking) => [
                    'check_in' => $booking->check_in->toDateString(),
                    'check_out' => $booking->check_out->toDateString(),
                ])
                ->values();
        @endphp

        <div class="grid gap-4 lg:grid-cols-4">
            <div class="lg:col-span-3">
                <div
                    class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                    @if ($primaryPhoto)
                        <img src="{{ $primaryPhoto->resolved_url }}" alt="{{ $property->title }}"
                             class="h-[420px] w-full object-cover">
                    @else
                        <div
                            class="flex h-[420px] items-center justify-center bg-zinc-100 text-zinc-500 dark:bg-zinc-800">
                            Немає фото
                        </div>
                    @endif
                </div>

                @if ($photos->count() > 1)
                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach ($photos->skip(1) as $photo)
                            <div
                                class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                                <img src="{{ $photo->resolved_url }}" alt="Фото {{ $loop->iteration }}"
                                     class="h-24 w-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-8 rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">Опис</h2>
                    <p class="mt-3 whitespace-pre-line break-words [overflow-wrap:anywhere] text-zinc-700 dark:text-zinc-200">{{ $property->description }}</p>
                </div>

                <div class="mt-6 rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">Локація</h2>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ $property->settlement?->name ?? '—' }}@if($property->settlement?->region)
                            , {{ $property->settlement->region }}
                        @endif, {{ $property->address }}</p>
                    <div class="mt-4 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <iframe
                            title="Локація об'єкта"
                            src="https://maps.google.com/maps?q={{ $mapQuery }}&z=14&output=embed"
                            class="h-80 w-full"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>

            <aside class="lg:col-span-1">
                <div
                    class="sticky top-6 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="text-2xl font-semibold text-zinc-900 dark:text-white">
                        {{ number_format((float) $property->price_per_night, 0, '.', ' ') }} ₴
                        <span class="text-sm font-normal text-zinc-500 dark:text-zinc-400">/ ніч</span>
                    </div>

                    <div class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                        Мінімум: {{ $property->min_nights }} ночей
                    </div>
                    @if($activeConfirmedBooking)
                        <div class="mt-2 rounded-lg bg-amber-100 px-3 py-2 text-xs font-medium text-amber-800">
                            Зараз об'єкт зайнятий до {{ $activeConfirmedBooking->check_out->format('d.m.Y') }}
                        </div>
                    @endif

                    <div class="mt-4 text-sm text-zinc-700 dark:text-zinc-200">
                        Середній рейтинг: <strong>{{ number_format($property->avg_rating, 1) }}</strong> / 5
                    </div>

                    @if(auth()->check() && auth()->id() === $property->user_id)
                        <div
                            class="mt-5 rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-3 text-sm text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800/60 dark:text-zinc-200">
                            Це ваше оголошення.
                        </div>
                    @else
                        <form id="booking-form" method="POST" action="{{ route('property.book', $property) }}"
                              class="mt-5 space-y-4"
                              data-unavailable-ranges='@json($unavailableRanges)'
                              data-max-booking-date="{{ $maxBookingDate }}">
                            @csrf

                            <div>
                                <label for="check_in"
                                       class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200">Дата
                                    заїзду</label>
                                <input
                                    id="check_in"
                                    type="date"
                                    name="check_in"
                                    min="{{ now()->toDateString() }}"
                                    max="{{ $maxBookingDate }}"
                                    value="{{ old('check_in', $checkInDefault) }}"
                                    class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none ring-blue-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                @error('check_in')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="check_out"
                                       class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200">Дата
                                    виїзду</label>
                                <input
                                    id="check_out"
                                    type="date"
                                    name="check_out"
                                    min="{{ now()->addDay()->toDateString() }}"
                                    max="{{ $maxBookingDate }}"
                                    value="{{ old('check_out', $checkOutDefault) }}"
                                    class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none ring-blue-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                @error('check_out')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="comment"
                                       class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200">
                                    Коментар до бронювання
                                </label>
                                <textarea
                                    id="comment"
                                    name="comment"
                                    maxlength="1000"
                                    rows="4"
                                    placeholder="Додайте побажання або деталі (до 1000 символів)"
                                    class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none ring-blue-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">{{ old('comment') }}</textarea>
                                @error('comment')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <p id="booking-conflict-error" class="hidden text-xs text-red-600"></p>

                            @if($unavailableRanges->isNotEmpty())
                                <div
                                    class="rounded-lg border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800/60">
                                    <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-200">Зайняті дати</p>
                                    <div id="occupied-calendar" class="mt-2 space-y-3 text-[11px]"></div>
                                    <div class="mt-3 space-y-1">
                                        @foreach($unavailableRanges as $range)
                                            <p class="text-xs text-zinc-600 dark:text-zinc-300">
                                                {{ Carbon::parse($range['check_in'])->format('d.m.Y') }}
                                                - {{ Carbon::parse($range['check_out'])->format('d.m.Y') }}
                                            </p>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div
                                    class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-800 dark:border-emerald-800/60 dark:bg-emerald-900/20 dark:text-emerald-300">
                                    Наразі об'єкт вільний: ви можете обирати будь-які дати в межах доступного періоду
                                    бронювання.
                                </div>
                            @endif

                            @auth
                                <button type="submit"
                                        class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                                    Забронювати
                                </button>
                            @else
                                <a href="{{ route('login') }}"
                                   class="block w-full rounded-lg bg-blue-600 px-4 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-700">
                                    Увійдіть, щоб забронювати
                                </a>
                            @endauth
                        </form>
                    @endif
                </div>
            </aside>
        </div>
    </section>
@endsection

@php use Illuminate\Support\Str; @endphp
@extends('layouts.app')

@section('main')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="flex items-center gap-3 text-2xl font-semibold text-zinc-900 dark:text-white">
                <span>Мої оренди</span>
                @if(($pendingRequests?->count() ?? 0) > 0)
                    <span class="inline-flex min-w-7 items-center justify-center rounded-full bg-amber-500 px-2 py-0.5 text-sm font-semibold text-white">
                        {{ $pendingRequests->count() }}
                    </span>
                @endif
            </h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">Ваші поточні та майбутні оренди.</p>
        </div>

        @php
            $sections = [
                'Зараз орендую' => $activeRentals,
                'Заплановані оренди' => $upcomingRentals,
                'Мої заявки в очікуванні' => $pendingRequests,
                'Потрібно оцінити' => $reviewRequiredRentals,
                'Історія оренд' => $rentalHistory,
            ];
        @endphp

        @forelse($sections as $title => $items)
            <div class="mb-8">
                <h2 class="mb-3 text-lg font-semibold text-zinc-900 dark:text-white">{{ $title }}</h2>

                @if($items->isEmpty())
                    <div
                        class="rounded-2xl border border-zinc-200 bg-white p-5 text-sm text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                        Немає записів у цьому розділі.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($items as $booking)
                            @php
                                $cover = $booking->property->photos->sortBy('position')->first()?->url;
                                $coverUrl = $cover
                                    ? (Str::startsWith($cover, ['http://', 'https://']) ? $cover : Storage::url($cover))
                                    : null;
                            @endphp

                            <article
                                class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                                <div class="grid gap-4 p-4 md:grid-cols-[220px_1fr]">
                                    <a href="{{ route('property.show', $booking->property) }}"
                                       class="block overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800">
                                        @if($coverUrl)
                                            <img src="{{ $coverUrl }}" alt="{{ $booking->property->title }}"
                                                 class="h-36 w-full object-cover md:h-full">
                                        @else
                                            <div
                                                class="flex h-36 items-center justify-center text-sm text-zinc-500 md:h-full">
                                                Немає фото
                                            </div>
                                        @endif
                                    </a>

                                    <div>
                                        <a href="{{ route('property.show', $booking->property) }}"
                                           class="text-lg font-medium text-zinc-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400">
                                            {{ $booking->property->title }}
                                        </a>
                                        <div class="mt-2">
                                            <span class="rounded-full px-2 py-1 text-xs font-medium
                                                @if($booking->status === 'pending') bg-amber-100 text-amber-800
                                                @elseif($booking->status === 'confirmed') bg-green-100 text-green-800
                                                @elseif($booking->status === 'cancelled') bg-zinc-200 text-zinc-700
                                                @elseif($booking->status === 'check_out') bg-blue-100 text-blue-800
                                                @else bg-red-100 text-red-800 @endif">
                                                @if($booking->status === 'pending')
                                                    Очікує
                                                @elseif($booking->status === 'confirmed')
                                                    Підтверджено
                                                @elseif($booking->status === 'cancelled')
                                                    Скасовано
                                                @elseif($booking->status === 'check_out')
                                                    Завершено
                                                @else
                                                    {{ $booking->status }}
                                                @endif
                                            </span>
                                        </div>
                                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                                            {{ $booking->check_in->format('d.m.Y') }}
                                            - {{ $booking->check_out->format('d.m.Y') }} ({{ $booking->nights_count }}
                                            ночей)
                                        </p>
                                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                                            Сума: {{ number_format((float) $booking->total_price, 0, '.', ' ') }} ₴
                                        </p>
                                        @if($booking->comment)
                                            <p class="mt-3 whitespace-pre-line text-sm text-zinc-700 dark:text-zinc-200">{{ $booking->comment }}</p>
                                        @endif

                                        @if($booking->status === 'confirmed' && $booking->check_out->startOfDay()->gt(now()->startOfDay()))
                                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                                <form method="POST"
                                                      action="{{ route('profile.my-rentals.request-cancellation', $booking) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            class="rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800">
                                                        @if($booking->cancellation_requested_by_tenant_at)
                                                            Ви підтвердили скасування
                                                        @elseif($booking->cancellation_requested_by_owner_at)
                                                            Підтвердити скасування орендодавця
                                                        @else
                                                            Запросити скасування
                                                        @endif
                                                    </button>
                                                </form>

                                                @if($booking->cancellation_requested_by_owner_at && !$booking->cancellation_requested_by_tenant_at)
                                                    <p class="text-xs text-zinc-600 dark:text-zinc-300">Орендодавець уже
                                                        підтвердив скасування.</p>
                                                @elseif($booking->cancellation_requested_by_tenant_at && !$booking->cancellation_requested_by_owner_at)
                                                    <p class="text-xs text-zinc-600 dark:text-zinc-300">Очікується
                                                        підтвердження орендодавця.</p>
                                                @endif
                                            </div>
                                        @endif

                                        @if($booking->check_out->startOfDay()->lte(now()->startOfDay()) && !$booking->review)
                                            <form method="POST"
                                                  action="{{ route('profile.my-rentals.review', $booking) }}"
                                                  class="mt-4 rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                                                @csrf
                                                <label
                                                    class="mb-2 block text-xs font-medium text-zinc-700 dark:text-zinc-200">
                                                    Залиште оцінку оренди (обов'язково)
                                                </label>
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <select name="rating" required
                                                            class="rounded-lg border border-zinc-300 bg-white px-2 py-1 text-sm text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                                        <option value="">Оцінка</option>
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <option value="{{ $i }}">{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                    <button type="submit"
                                                            class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700">
                                                        Зберегти оцінку
                                                    </button>
                                                </div>
                                            </form>
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

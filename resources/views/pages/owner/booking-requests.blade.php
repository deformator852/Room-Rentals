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
            <h1 class="flex items-center gap-3 text-2xl font-semibold text-zinc-900 dark:text-white">
                <span>Запити на оренду</span>
                @if(($pendingBookingsCount ?? 0) > 0)
                    <span class="inline-flex min-w-7 items-center justify-center rounded-full bg-amber-500 px-2 py-0.5 text-sm font-semibold text-white">
                        {{ $pendingBookingsCount }}
                    </span>
                @endif
            </h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">Обробляйте запити клієнтів по вашим об'єктам.</p>
        </div>
        <div class="space-y-4">
            @forelse($bookings as $booking)
                <article class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-lg font-medium text-zinc-900 dark:text-white">{{ $booking->property->title }}</p>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $booking->check_in->format('d.m.Y') }} - {{ $booking->check_out->format('d.m.Y') }}
                                ({{ $booking->nights_count }} ночей)
                            </p>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                                Сума: {{ number_format((float) $booking->total_price, 0, '.', ' ') }} ₴</p>
                        </div>

                        <span class="rounded-full px-3 py-1 text-xs font-medium
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
                                Відхилено
                            @endif
                        </span>
                    </div>

                    <div class="mt-4 rounded-xl bg-zinc-50 p-4 dark:bg-zinc-800/50">
                        <p class="text-sm font-medium text-zinc-900 dark:text-white">Клієнт</p>
                        <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-200">{{ $booking->tenant->name }}</p>
                        <p class="text-sm text-zinc-600 dark:text-zinc-300">{{ $booking->tenant->email }}</p>
                        @if($booking->comment)
                            <p class="mt-3 text-sm font-medium text-zinc-900 dark:text-white">Коментар клієнта</p>
                            <p class="mt-1 whitespace-pre-line text-sm text-zinc-700 dark:text-zinc-200">{{ $booking->comment }}</p>
                        @endif
                    </div>

                    @if($booking->status === 'pending')
                        <div class="mt-4 flex gap-2">
                            <form method="POST" action="{{ route('owner.booking-requests.approve', $booking) }}">
                                @csrf
                                <button type="submit"
                                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                                    Підтвердити
                                </button>
                            </form>

                            <form method="POST" action="{{ route('owner.booking-requests.reject', $booking) }}">
                                @csrf
                                <button type="submit"
                                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                                    Відхилити
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($booking->status === 'confirmed' && $booking->check_out->startOfDay()->gt(now()->startOfDay()))
                        <div class="mt-4 flex gap-2">
                            <form method="POST"
                                  action="{{ route('owner.booking-requests.cancel-confirmed', $booking) }}">
                                @csrf
                                <button type="submit"
                                        class="rounded-lg cursor-pointer bg-red-700 px-4 py-2 text-sm font-medium text-white hover:bg-red-800">
                                    @if($booking->cancellation_requested_by_owner_at)
                                        Ви підтвердили скасування
                                    @elseif($booking->cancellation_requested_by_tenant_at)
                                        Підтвердити скасування орендаря
                                    @else
                                        Запросити скасування
                                    @endif
                                </button>
                            </form>
                        </div>

                        @if($booking->cancellation_requested_by_tenant_at && !$booking->cancellation_requested_by_owner_at)
                            <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-300">Орендар уже підтвердив
                                скасування.</p>
                        @elseif($booking->cancellation_requested_by_owner_at && !$booking->cancellation_requested_by_tenant_at)
                            <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-300">Очікується підтвердження
                                орендаря.</p>
                        @endif
                    @endif
                </article>
            @empty
                <div
                    class="rounded-2xl border border-zinc-200 bg-white p-8 text-center text-zinc-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                    Тут з'являться всі заявки на оренду ваших об'єктів.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    </section>
@endsection

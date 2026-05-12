<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">
                Мої оголошення
            </h1>

            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Керуйте своїми оголошеннями
            </p>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    @if($properties->isEmpty())

        <div
            class="rounded-2xl border border-dashed border-zinc-300 bg-white p-10 text-center dark:border-zinc-700 dark:bg-zinc-900">

            <h2 class="text-lg font-medium text-zinc-900 dark:text-white">
                У вас поки немає оголошень
            </h2>

            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                Створіть своє перше оголошення
            </p>

        </div>

    @else

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">

            @foreach($properties as $property)

                @php
                    $mainPhoto = $property->mainPhoto->first();
                    $mainPhotoUrl = $mainPhoto
                        ? (\Illuminate\Support\Str::startsWith($mainPhoto->url, ['http://', 'https://'])
                            ? $mainPhoto->url
                            : Storage::url($mainPhoto->url))
                        : null;
                @endphp

                <div
                    class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">

                    <div class="aspect-[4/3] overflow-hidden bg-zinc-100 dark:bg-zinc-800">

                        @if($mainPhotoUrl)

                            <img
                                src="{{ $mainPhotoUrl }}"
                                alt="{{ $property->title }}"
                                class="h-full w-full object-cover"
                            >

                        @else

                            <div class="flex h-full items-center justify-center text-zinc-400">
                                Немає фото
                            </div>

                        @endif

                    </div>

                    <div class="p-4">

                        <div class="mb-3 flex items-start justify-between gap-3">

                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                {{ $property->property_type->icon() }}
                                {{ $property->property_type->label() }}
                            </span>

                            <span
    @class([
        'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold text-white',

        'bg-yellow-400' => $property->status->value === 'pending',
        'bg-green-500' => $property->status->value === 'published',
        'bg-red-500' => $property->status->value === 'rejected',
        'bg-blue-500' => $property->status->value === 'draft',
        'bg-zinc-500' => $property->status->value === 'inactive',
    ])
>
    {{ $property->status->label() }}
</span>

                        </div>

                        <h2 class="line-clamp-1 text-lg font-semibold text-zinc-900 dark:text-white">
                            {{ $property->title }}
                        </h2>

                        <p class="mt-1 line-clamp-1 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $property->settlement?->name ?? '—' }}@if($property->settlement?->region), {{ $property->settlement->region }}@endif
                        </p>

                        <div class="mt-3 flex items-center gap-3 text-sm text-zinc-500 dark:text-zinc-400">
                            <span>{{ $property->rooms_count }} кімн.</span>
                            <span>{{ $property->area }} м²</span>
                        </div>

                        <div class="mt-4 flex items-center justify-between">

                            <div>
                                <span class="text-lg font-semibold text-zinc-900 dark:text-white">
                                    {{ number_format($property->price_per_night, 0, '.', ' ') }} ₴
                                </span>

                                <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                    / ніч
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <a
                                    href="{{ route('property.edit', $property) }}"
                                    class="rounded-lg border border-zinc-200 px-3 py-1.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800 transition-colors">
                                    Редагувати
                                </a>

                                @if($property->status->value === 'published')
                                    <button
                                        type="button"
                                        wire:click="suspend('{{ $property->id }}')"
                                        class="rounded-lg bg-amber-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-amber-700 transition-colors">
                                        Призупинити
                                    </button>
                                @elseif($property->status->value === 'inactive')
                                    <button
                                        type="button"
                                        wire:click="resume('{{ $property->id }}')"
                                        class="rounded-lg bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700 transition-colors">
                                        Відновити
                                    </button>
                                @endif
                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

        <div class="mt-8">
            {{ $properties->links() }}
        </div>

    @endif

</div>

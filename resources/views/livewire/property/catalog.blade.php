<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="mb-8 flex items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Оренда нерухомості</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Знайдіть доступне житло за фільтрами нижче.</p>
        </div>
        <div class="text-sm text-zinc-500 dark:text-zinc-400">
            Знайдено: {{ $properties->total() }}
        </div>
    </div>

    <div class="mb-7 rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
            <div>
                <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Населений пункт</label>
                <div class="relative">
                    <input wire:model.live.debounce.300ms="settlementQuery" type="text" placeholder="Почніть вводити назву"
                           class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none ring-blue-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">

                    @if(!$settlementId && mb_strlen(trim($settlementQuery)) >= 2 && $settlementSuggestions->isNotEmpty())
                        <div class="absolute z-20 mt-1 max-h-56 w-full overflow-auto rounded-lg border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
                            @foreach($settlementSuggestions as $settlement)
                                <button type="button" wire:click="selectSettlement({{ $settlement->id }})"
                                        class="block w-full px-3 py-2 text-left text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-800">
                                    {{ $settlement->name }}
                                    @if($settlement->region)
                                        <span class="text-zinc-500">, {{ $settlement->region }}</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Тип об'єкта</label>
                <select wire:model="propertyType"
                        class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none ring-blue-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Будь-який</option>
                    @foreach($propertyTypes as $value => $type)
                        <option value="{{ $value }}">{{ $type['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Ціна від, ₴</label>
                <input wire:model="priceMin" type="number" min="0" placeholder="1000"
                       class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none ring-blue-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Ціна до, ₴</label>
                <input wire:model="priceMax" type="number" min="0" placeholder="5000"
                       class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none ring-blue-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Кімнати</label>
                <select wire:model="roomsCount"
                        class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none ring-blue-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Будь-яка</option>
                    @foreach(range(1, 10) as $n)
                        <option value="{{ $n }}">{{ $n }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Доступно з</label>
                <input wire:model="availableFrom" type="date"
                       class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none ring-blue-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Доступно до</label>
                <input wire:model="availableTo" type="date"
                       class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none ring-blue-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Сортування</label>
                <select wire:model="sort"
                        class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 outline-none ring-blue-500 focus:ring-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="rating_desc">Рейтинг: спочатку вищі</option>
                    <option value="rating_asc">Рейтинг: спочатку нижчі</option>
                    <option value="price_asc">Ціна: зростання</option>
                    <option value="price_desc">Ціна: спадання</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Фільтри та пагінація працюють без перезавантаження сторінки.</p>
            <div class="flex items-center gap-2">
                <button wire:click="clearFilters" wire:loading.attr="disabled" wire:target="applyCatalogFilters,clearFilters,gotoPage,nextPage,previousPage,setPage" type="button"
                        class="rounded-lg border border-zinc-300 px-3 py-1.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                    Очистити
                </button>
                <button wire:click="applyCatalogFilters" wire:loading.attr="disabled" wire:target="applyCatalogFilters,clearFilters,gotoPage,nextPage,previousPage,setPage" type="button"
                        class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">
                    Застосувати фільтри
                </button>
            </div>
        </div>
    </div>

    <div class="relative">
        <div wire:loading.flex wire:target="applyCatalogFilters,clearFilters,gotoPage,nextPage,previousPage,setPage"
             class="pointer-events-none absolute inset-0 z-10 items-center justify-center rounded-2xl bg-white/60 backdrop-blur-[1px] dark:bg-zinc-900/60">
            <div class="flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-3 py-1.5 text-sm text-zinc-600 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                <span class="inline-block h-2 w-2 animate-pulse rounded-full bg-blue-500"></span>
                Оновлюємо список...
            </div>
        </div>

        @if ($properties->isEmpty())
            <div class="rounded-2xl border border-dashed border-zinc-300 bg-white p-10 text-center dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-medium text-zinc-900 dark:text-white">Нічого не знайдено</h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Спробуйте змінити фільтри або дати.</p>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
                 wire:loading.class="opacity-60"
                 wire:target="applyCatalogFilters,clearFilters,gotoPage,nextPage,previousPage,setPage">
                @foreach ($properties as $property)
                @php
                    $cover = $property->mainPhoto->first()?->url ?? $property->photos->sortBy('position')->first()?->url;
                    $coverUrl = $cover
                        ? (\Illuminate\Support\Str::startsWith($cover, ['http://', 'https://']) ? $cover : Storage::url($cover))
                        : null;
                @endphp

                <article class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
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

                        <p class="mt-1 line-clamp-1 text-sm text-zinc-600 dark:text-zinc-300">{{ $property->settlement?->name ?? '—' }}@if($property->settlement?->region), {{ $property->settlement->region }}@endif</p>

                        <div class="mt-3 flex items-center justify-between text-sm text-zinc-600 dark:text-zinc-300">
                            <span>{{ $property->property_type->label() }}</span>
                            <span>⭐ {{ number_format($property->avg_rating, 1) }}</span>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <div class="text-xl font-semibold text-zinc-900 dark:text-white">
                                {{ number_format((float) $property->price_per_night, 0, '.', ' ') }} ₴
                                <span class="text-sm font-normal text-zinc-500 dark:text-zinc-400">/ добу</span>
                            </div>
                            <a href="{{ route('property.show', $property) }}" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Переглянути
                            </a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $properties->links() }}
            </div>
        @endif
    </div>
</div>

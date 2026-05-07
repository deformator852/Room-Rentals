<div class="mx-auto max-w-2xl px-4 py-10">

    <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">
        Редагування оголошення
    </h1>

    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
        Оновіть інформацію про ваше житло
    </p>

    @if (session('status'))
        <div
            class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    <div class="mt-8 space-y-8">

        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-zinc-400">
                Тип нерухомості
            </p>

            <div class="grid grid-cols-3 gap-3 sm:grid-cols-6">

                @foreach ($propertyTypes as $value => $data)

                    <label class="cursor-pointer">

                        <input
                            type="radio"
                            wire:model="property_type"
                            value="{{ $value }}"
                            class="peer sr-only"
                        />

                        <div
                            class="flex flex-col items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-2 py-3 text-center transition-all
                            hover:border-blue-400 hover:bg-blue-50
                            peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:ring-1 peer-checked:ring-blue-500
                            dark:border-zinc-700 dark:bg-zinc-800/60
                            dark:hover:border-blue-500 dark:hover:bg-blue-950/30
                            dark:peer-checked:border-blue-500 dark:peer-checked:bg-blue-950/40 dark:peer-checked:ring-blue-500">

                            <span class="text-xl leading-none">
                                {{ $data['icon'] }}
                            </span>

                            <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">
                                {{ $data['label'] }}
                            </span>

                        </div>

                    </label>

                @endforeach

            </div>

            @error('property_type')
            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-zinc-400">
                Основна інформація
            </p>

            <div class="space-y-5">

                <div>

                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Заголовок
                    </label>

                    <input
                        wire:model="title"
                        type="text"
                        maxlength="100"
                        placeholder="Наприклад: Затишна квартира в центрі Києва"
                        class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500"
                    />

                    @error('title')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                </div>

                <div>

                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Опис
                    </label>

                    <textarea
                        wire:model="description"
                        rows="4"
                        maxlength="2000"
                        placeholder="Розкажіть про особливості житла, зручності, розташування..."
                        class="w-full resize-y rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500"
                    ></textarea>

                    @error('description')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                </div>

            </div>
        </div>

        <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

        <div>

            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-zinc-400">
                Розташування
            </p>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                <div>

                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Місто
                    </label>

                    <input
                        wire:model="city"
                        type="text"
                        placeholder="Київ"
                        class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500"
                    />

                    @error('city')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                </div>

                <div>

                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Адреса
                    </label>

                    <input
                        wire:model="address"
                        type="text"
                        placeholder="Вул. Хрещатик, 1"
                        class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500"
                    />

                    @error('address')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                </div>

            </div>

        </div>

        <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

        <div>

            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-zinc-400">
                Характеристики
            </p>

            <div class="grid grid-cols-2 gap-5 sm:grid-cols-4">

                <div>

                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Кімнат
                    </label>

                    <select
                        wire:model="rooms_count"
                        @disabled($property_type === 'room')
                        class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    >
                        <option value="">—</option>

                        @foreach (range(1, 10) as $n)
                            <option value="{{ $n }}">{{ $n }}</option>
                        @endforeach

                    </select>

                    @if($property_type === 'room')
                        <p class="mt-1 text-xs text-zinc-500">
                            Для кімнати кількість кімнат завжди = 1
                        </p>
                    @endif

                    @error('rooms_count')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                </div>

                <div>

                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Площа
                    </label>

                    <div
                        class="flex overflow-hidden rounded-lg border border-zinc-200 bg-white focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800">

                        <input
                            wire:model="area"
                            type="number"
                            min="1"
                            placeholder="50"
                            class="w-full bg-transparent px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 outline-none dark:text-white dark:placeholder-zinc-500"
                        />

                        <span
                            class="flex items-center border-l border-zinc-200 bg-zinc-50 px-3 text-xs font-medium text-zinc-400 dark:border-zinc-700 dark:bg-zinc-700/50">
                            м²
                        </span>

                    </div>

                    @error('area')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                </div>

                <div>

                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Ціна / ніч
                    </label>

                    <div
                        class="flex overflow-hidden rounded-lg border border-zinc-200 bg-white focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800">

                        <span
                            class="flex items-center border-r border-zinc-200 bg-zinc-50 px-3 text-xs font-medium text-zinc-400 dark:border-zinc-700 dark:bg-zinc-700/50">
                            ₴
                        </span>

                        <input
                            wire:model="price_per_night"
                            type="number"
                            min="0"
                            placeholder="800"
                            class="w-full bg-transparent px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 outline-none dark:text-white dark:placeholder-zinc-500"
                        />

                    </div>

                    @error('price_per_night')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                </div>

                <div>

                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Мін. ночей
                    </label>

                    <input
                        wire:model="min_nights"
                        type="number"
                        min="1"
                        class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500"
                    />

                    @error('min_nights')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                </div>

            </div>

        </div>

        <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

        <div
            class="rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">
            Після редагування оголошення буде повторно відправлено на <strong>модерацію</strong>.
        </div>

        <div class="flex justify-end gap-3">

            <a
                href="{{ route('profile.my-properties') }}"
                class="rounded-lg border border-zinc-200 bg-white px-5 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
            >
                Скасувати
            </a>

            <button
                wire:click="update"
                wire:loading.attr="disabled"
                type="button"
                class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-60 transition-colors"
            >

                <span wire:loading.remove wire:target="update">
                    Зберегти зміни
                </span>

                <span wire:loading wire:target="update">
                    Збереження...
                </span>

            </button>

        </div>

    </div>

</div>

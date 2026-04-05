<div class="mx-auto max-w-2xl px-4 py-10">

    <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">Нове оголошення</h1>
    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Заповніть інформацію про ваше житло</p>

    @if (session('status'))
        <div
            class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    <div class="mt-8 space-y-8">

        {{-- TYPE --}}
        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-zinc-400">Тип нерухомості</p>
            <div class="grid grid-cols-3 gap-3 sm:grid-cols-6">
                @foreach ([['value' => 'apartment', 'icon' => '🏢', 'label' => 'Квартира'], ['value' => 'house', 'icon' => '🏠', 'label' => 'Будинок'], ['value' => 'room', 'icon' => '🛏️', 'label' => 'Кімната'], ['value' => 'studio', 'icon' => '🏙️', 'label' => 'Студія'], ['value' => 'cottage', 'icon' => '🌿', 'label' => 'Котедж'], ['value' => 'other', 'icon' => '📦', 'label' => 'Інше']] as $type)
                    <label class="cursor-pointer">
                        <input type="radio" wire:model="property_type" value="{{ $type['value'] }}"
                            class="peer sr-only" />
                        <div
                            class="flex flex-col items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-2 py-3 text-center transition-all
                                    hover:border-blue-400 hover:bg-blue-50
                                    peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:ring-1 peer-checked:ring-blue-500
                                    dark:border-zinc-700 dark:bg-zinc-800/60
                                    dark:hover:border-blue-500 dark:hover:bg-blue-950/30
                                    dark:peer-checked:border-blue-500 dark:peer-checked:bg-blue-950/40 dark:peer-checked:ring-blue-500">
                            <span class="text-xl leading-none">{{ $type['icon'] }}</span>
                            <span
                                class="text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ $type['label'] }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('property_type')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

        {{-- PHOTOS --}}
        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-zinc-400">Фотографії</p>

            <label
                class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-zinc-200 bg-white px-6 py-8 transition-colors
                          hover:border-blue-400 hover:bg-blue-50
                          dark:border-zinc-700 dark:bg-zinc-800/40 dark:hover:border-blue-500 dark:hover:bg-blue-950/20">
                <input type="file" wire:model="photos" class="hidden" accept="image/*" multiple />
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-100 text-zinc-400 dark:bg-zinc-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5l4.5-4.5 4.5 4.5 4.5-6 4.5 6" />
                        <rect x="3" y="3" width="18" height="18" rx="3" stroke-linecap="round" />
                    </svg>
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Натисніть або перетягніть фото</p>
                    <p class="mt-0.5 text-xs text-zinc-400">JPG, PNG, WebP — до 5MB. Перше фото буде головним</p>
                </div>
            </label>

            @if (!empty($photos))
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($photos as $i => $photo)
                        <div class="relative">
                            <img src="{{ $photo->temporaryUrl() }}"
                                class="h-20 w-20 rounded-lg object-cover ring-2 ring-zinc-200 dark:ring-zinc-700" />
                            @if ($i === 0)
                                <span
                                    class="absolute bottom-0 left-0 right-0 rounded-b-lg bg-blue-600/80 py-0.5 text-center text-[9px] font-semibold text-white">
                                    Головне
                                </span>
                            @endif
                            <button wire:click="removePhoto({{ $i }})" type="button"
                                class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-zinc-800/70 text-white hover:bg-red-500 transition-colors text-xs leading-none">
                                ×
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            @error('photos.*')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

        {{-- BASIC INFO --}}
        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-zinc-400">Основна інформація</p>
            <div class="space-y-5">

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Заголовок</label>
                    <input wire:model="title" type="text" maxlength="100"
                        placeholder="Наприклад: Затишна квартира в центрі Києва"
                        class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500" />
                    @error('title')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Опис</label>
                    <textarea wire:model="description" rows="4" maxlength="2000"
                        placeholder="Розкажіть про особливості житла, зручності, розташування..."
                        class="w-full resize-y rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500"></textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

        {{-- LOCATION --}}
        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-zinc-400">Розташування</p>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Місто</label>
                    <input wire:model="city" type="text" placeholder="Київ"
                        class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500" />
                    @error('city')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Адреса</label>
                    <input wire:model="address" type="text" placeholder="Вул. Хрещатик, 1"
                        class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500" />
                    @error('address')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

        {{-- DETAILS --}}
        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-zinc-400">Характеристики</p>
            <div class="grid grid-cols-2 gap-5 sm:grid-cols-4">

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Кімнат</label>
                    <select wire:model="rooms_count"
                        class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        <option value="">—</option>
                        @foreach (range(1, 10) as $n)
                            <option value="{{ $n }}">{{ $n }}</option>
                        @endforeach
                    </select>
                    @error('rooms_count')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Площа</label>
                    <div
                        class="flex overflow-hidden rounded-lg border border-zinc-200 bg-white focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800">
                        <input wire:model="area" type="number" min="1" placeholder="50"
                            class="w-full bg-transparent px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 outline-none dark:text-white dark:placeholder-zinc-500" />
                        <span
                            class="flex items-center border-l border-zinc-200 bg-zinc-50 px-3 text-xs font-medium text-zinc-400 dark:border-zinc-700 dark:bg-zinc-700/50">м²</span>
                    </div>
                    @error('area')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Ціна / ніч</label>
                    <div
                        class="flex overflow-hidden rounded-lg border border-zinc-200 bg-white focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800">
                        <span
                            class="flex items-center border-r border-zinc-200 bg-zinc-50 px-3 text-xs font-medium text-zinc-400 dark:border-zinc-700 dark:bg-zinc-700/50">₴</span>
                        <input wire:model="price_per_night" type="number" min="0" placeholder="800"
                            class="w-full bg-transparent px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 outline-none dark:text-white dark:placeholder-zinc-500" />
                    </div>
                    @error('price_per_night')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Мін. ночей</label>
                    <input wire:model="min_nights" type="number" min="1"
                        class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500" />
                    @error('min_nights')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

        {{-- NOTICE --}}
        <div class="rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">
            Після публікації оголошення буде відправлено на <strong>модерацію</strong>. Зазвичай перевірка займає до 24
            годин.
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-end gap-3">
            <button wire:click="saveDraft" wire:loading.attr="disabled" type="button"
                class="rounded-lg border border-zinc-200 bg-white px-5 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 disabled:opacity-60 transition-colors dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                <span wire:loading.remove wire:target="saveDraft">Зберегти чернетку</span>
                <span wire:loading wire:target="saveDraft">Збереження...</span>
            </button>

            <button wire:click="publish" wire:loading.attr="disabled" type="button"
                class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-60 transition-colors">
                <span wire:loading.remove wire:target="publish">Опублікувати</span>
                <span wire:loading wire:target="publish">Публікуємо...</span>
            </button>
        </div>

    </div>
</div>

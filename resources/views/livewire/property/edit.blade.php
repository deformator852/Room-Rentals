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
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-zinc-400">Фотографії</p>

            @php
                $existingPhotos = $property->photos->sortBy('position');
                $activeExistingPhotos = $existingPhotos->filter(fn ($photo) => !in_array($photo->id, $deletedExistingPhotoIds, true));
                $activeExistingPhotosCount = $activeExistingPhotos->count();
            @endphp

            <div>
                <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Фото</label>

                <div wire:ignore id="edit-photo-upload-zone"
                     class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-zinc-200 bg-white px-6 py-8 transition-all hover:border-blue-400 hover:bg-blue-50 dark:border-zinc-700 dark:bg-zinc-800/40 dark:hover:border-blue-500 dark:hover:bg-blue-950/20">
                    <input type="file" id="edit-photo-input" class="hidden" accept="image/jpeg,image/png,image/webp" multiple/>

                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-100 text-zinc-400 dark:bg-zinc-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5l4.5-4.5 4.5 4.5 4.5-6 4.5 6"/>
                            <rect x="3" y="3" width="18" height="18" rx="3" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div class="text-center">
                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Натисніть або перетягніть фото</p>
                        <p class="mt-0.5 text-xs text-zinc-400">JPG, PNG, WebP — до 5 МБ</p>
                        <p id="edit-photo-count"
                           data-existing-count="{{ $activeExistingPhotosCount }}"
                           class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $activeExistingPhotosCount }} фото
                        </p>
                    </div>
                </div>

                <div id="edit-photo-previews" class="mt-6">
                    <div id="edit-previews-container" class="flex flex-wrap gap-4">
                        @foreach($activeExistingPhotos as $photo)
                            @php
                                $photoUrl = \Illuminate\Support\Str::startsWith($photo->url, ['http://', 'https://'])
                                    ? $photo->url
                                    : Storage::url($photo->url);
                            @endphp

                            <div class="relative group w-28" data-existing-photo="1">
                                <img src="{{ $photoUrl }}" alt="Фото"
                                     class="h-28 w-28 rounded-xl object-cover ring-2 ring-zinc-200 dark:ring-zinc-700">

                                @if($photo->is_main)
                                    <span class="absolute -bottom-1 -left-1 rounded bg-blue-600 px-2 py-0.5 text-[10px] font-medium text-white">
                                        ГОЛОВНЕ
                                    </span>
                                @endif

                                <button type="button" wire:click="markExistingPhotoForDelete('{{ $photo->id }}')"
                                        class="absolute -top-2 -right-2 h-7 w-7 flex items-center justify-center rounded-full bg-red-500 text-xl leading-none text-white shadow-md transition hover:bg-red-600">
                                    ×
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @error('newPhotos')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
            @error('newPhotos.*')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
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
                        Населений пункт
                    </label>

                    <div class="relative">
                        <input
                            wire:model.live.debounce.300ms="settlementQuery"
                            type="text"
                            placeholder="Почніть вводити назву"
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500"
                        />

                        @if(!$settlement_id && mb_strlen(trim($settlementQuery)) >= 2 && $settlementSuggestions->isNotEmpty())
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

                    @error('settlement_id')
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
                id="update-property-btn"
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const zone = document.getElementById('edit-photo-upload-zone');
        let input = document.getElementById('edit-photo-input');
        const updateBtn = document.getElementById('update-property-btn');
        if (!zone || !input || !updateBtn) return;

        let selectedFiles = [];
        const countEl = document.getElementById('edit-photo-count');
        const previewsDiv = document.getElementById('edit-photo-previews');
        const previewsContainer = document.getElementById('edit-previews-container');

        function getComponentFromElement(el) {
            const root = el?.closest('[wire\\:id]');
            if (!root) return null;
            return window.Livewire.find(root.getAttribute('wire:id'));
        }

        function updateCount() {
            const existingCount = Number(countEl?.dataset?.existingCount ?? 0);
            const total = existingCount + selectedFiles.length;
            countEl.textContent = selectedFiles.length > 0
                ? `${total} фото (${selectedFiles.length} нових)`
                : `${total} фото`;

            previewsDiv?.classList.remove('hidden');
        }

        function renderPreviews() {
            if (!previewsContainer) return;

            previewsContainer.querySelectorAll('[data-new-photo="1"]').forEach(el => el.remove());

            selectedFiles.forEach((file, index) => {
                const div = document.createElement('div');
                div.className = 'relative group w-28';
                div.dataset.newPhoto = '1';
                const existingCount = Number(countEl?.dataset?.existingCount ?? 0);
                const isMainAfterSave = existingCount === 0 && index === 0;
                div.innerHTML = `
                    <img src="${URL.createObjectURL(file)}" class="h-28 w-28 rounded-xl object-cover ring-2 ring-zinc-200 dark:ring-zinc-700" />
                    ${isMainAfterSave ? '<span class="absolute -bottom-1 -left-1 rounded bg-blue-600 px-2 py-0.5 text-[10px] font-medium text-white">ГОЛОВНЕ</span>' : ''}
                    <button type="button" data-index="${index}" class="absolute -top-2 -right-2 h-7 w-7 flex items-center justify-center bg-red-500 hover:bg-red-600 text-white rounded-full text-xl leading-none shadow-md opacity-0 group-hover:opacity-100 transition">×</button>
                `;
                previewsContainer.appendChild(div);
            });

            previewsContainer.querySelectorAll('button').forEach(btn => {
                btn.addEventListener('click', function () {
                    const idx = parseInt(this.getAttribute('data-index'));
                    selectedFiles.splice(idx, 1);
                    renderPreviews();
                    updateCount();
                });
            });
        }

        function addFiles(files) {
            Array.from(files).forEach(file => {
                if (file.size > 5 * 1024 * 1024) return;
                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) return;
                if (selectedFiles.length < 7) {
                    selectedFiles.push(file);
                }
            });

            renderPreviews();
            updateCount();
        }

        function resetInput() {
            const newInput = input.cloneNode(true);
            input.parentNode.replaceChild(newInput, input);
            input = newInput;
            attachInputEvents();
        }

        function attachInputEvents() {
            input.addEventListener('change', () => {
                addFiles(input.files);
                resetInput();
            });
        }

        zone.addEventListener('click', () => input.click());
        zone.addEventListener('dragover', e => {
            e.preventDefault();
            zone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-950/30');
        });
        zone.addEventListener('dragleave', () => {
            zone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-950/30');
        });
        zone.addEventListener('drop', e => {
            e.preventDefault();
            zone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-950/30');
            addFiles(e.dataTransfer.files);
        });

        attachInputEvents();
        updateCount();

        window.Livewire?.on('existing-photos-count-updated', (payload) => {
            const count = Number(payload?.count ?? 0);
            if (countEl) {
                countEl.dataset.existingCount = String(count);
            }
            updateCount();
            renderPreviews();
        });

        function uploadAllFiles(component, callback) {
            if (selectedFiles.length === 0) {
                callback();
                return;
            }

            let uploaded = 0;
            const total = selectedFiles.length;

            selectedFiles.forEach(file => {
                component.upload('newPhotos', file,
                    () => {
                        uploaded++;
                        if (uploaded === total) callback();
                    },
                    () => alert('Помилка завантаження фото')
                );
            });
        }

        updateBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const root = updateBtn.closest('[wire\\:id]');
            if (!root) return;
            const component = window.Livewire.find(root.getAttribute('wire:id'));
            if (!component) return;

            uploadAllFiles(component, () => component.call('update'));
        });
    });
</script>

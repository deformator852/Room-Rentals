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

        <div>
            <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-zinc-400">Тип нерухомості</p>
            <div class="grid grid-cols-3 gap-3 sm:grid-cols-6">
                @foreach ($propertyTypes as $value => $data)
                    <label class="cursor-pointer">
                        <input type="radio" wire:model="property_type" value="{{ $value }}"
                            class="peer sr-only" />
                        <div
                            class="flex flex-col items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-2 py-3 text-center transition-all
                                    hover:border-blue-400 hover:bg-blue-50
                                    peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:ring-1 peer-checked:ring-blue-500
                                    dark:border-zinc-700 dark:bg-zinc-800/60
                                    dark:hover:border-blue-500 dark:hover:bg-blue-950/30
                                    dark:peer-checked:border-blue-500 dark:peer-checked:bg-blue-950/40 dark:peer-checked:ring-blue-500">
                            <span class="text-xl leading-none">{{ $data['icon'] }}</span>
                            <span
                                class="text-xs font-medium text-zinc-600 dark:text-zinc-400">{{ $data['label'] }}</span>
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

            <div id="photo-upload-zone"
                class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-zinc-200 bg-white px-6 py-8 transition-all hover:border-blue-400 hover:bg-blue-50 dark:border-zinc-700 dark:bg-zinc-800/40 dark:hover:border-blue-500 dark:hover:bg-blue-950/20 {{ count($photos) >= 7 ? 'opacity-50 cursor-not-allowed' : '' }}">

                <input type="file" id="photo-input" class="hidden" accept="image/jpeg,image/png,image/webp"
                    multiple />

                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-100 text-zinc-400 dark:bg-zinc-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5l4.5-4.5 4.5 4.5 4.5-6 4.5 6" />
                        <rect x="3" y="3" width="18" height="18" rx="3" stroke-linecap="round" />
                    </svg>
                </div>

                <div class="text-center">
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Натисніть або перетягніть фото</p>
                    <p class="mt-0.5 text-xs text-zinc-400">JPG, PNG, WebP — до 5 МБ. Можна вибирати кілька разів</p>
                    <p id="photo-count" class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">0/7 фотографій</p>
                </div>
            </div>

            <!-- Прев’ю (тільки фронт) -->
            <div id="photo-previews" class="mt-6 hidden">
                <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-zinc-400">
                    Вибрані фотографії (<span id="preview-count">0</span>/7)
                </p>
                <div id="previews-container" class="flex flex-wrap gap-4"></div>
            </div>

            @error('photos')
                <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
            @enderror
            @error('photos.*')
                <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

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

        <div class="rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">
            Після публікації оголошення буде відправлено на <strong>модерацію</strong>. Зазвичай перевірка займає до 24
            годин.
        </div>

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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const zone = document.getElementById('photo-upload-zone');
        let input = document.getElementById('photo-input');
        let selectedFiles = [];

        const previewsContainer = document.getElementById('previews-container');
        const previewsDiv = document.getElementById('photo-previews');
        const countEl = document.getElementById('photo-count');
        const previewCountEl = document.getElementById('preview-count');

        function updateCount() {
            const count = selectedFiles.length;
            countEl.textContent = `${count}/7 фотографій`;
            previewCountEl.textContent = count;
            previewsDiv.classList.toggle('hidden', count === 0);
        }

        function createPreview(file, index) {
            const div = document.createElement('div');
            div.className = 'relative group';
            div.innerHTML = `
            <img src="${URL.createObjectURL(file)}" class="h-28 w-28 rounded-xl object-cover ring-2 ring-zinc-200 dark:ring-zinc-700" />
            <button type="button" data-index="${index}" 
                    class="absolute -top-2 -right-2 h-7 w-7 flex items-center justify-center bg-red-500 hover:bg-red-600 text-white rounded-full text-xl leading-none shadow-md opacity-0 group-hover:opacity-100 transition">
                ×
            </button>
            ${index === 0 ? `<span class="absolute -bottom-1 -left-1 bg-blue-600 text-white text-[10px] px-2 py-0.5 rounded font-medium">ГОЛОВНЕ</span>` : ''}
        `;
            return div;
        }

        function renderPreviews() {
            previewsContainer.innerHTML = '';
            selectedFiles.forEach((file, i) => {
                const preview = createPreview(file, i);
                previewsContainer.appendChild(preview);
            });

            // Remove buttons
            previewsContainer.querySelectorAll('button').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idx = parseInt(this.getAttribute('data-index'));
                    URL.revokeObjectURL(URL.createObjectURL(selectedFiles[idx])); // cleanup
                    selectedFiles.splice(idx, 1);
                    renderPreviews();
                    updateCount();
                });
            });
        }

        function addFiles(files) {
            if (selectedFiles.length >= 7) return;

            Array.from(files).forEach(file => {
                if (file.size > 5 * 1024 * 1024) return; // 5MB
                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) return;

                selectedFiles.push(file);
            });

            if (selectedFiles.length > 7) selectedFiles = selectedFiles.slice(0, 7);

            renderPreviews();
            updateCount();
        }

        // Reset input (щоб можна було вибирати ще раз)
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

        zone.addEventListener('click', (e) => {
            if (e.target.tagName === 'INPUT') return;
            input.click();
        });

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

        function uploadAllFiles(callback) {
            if (selectedFiles.length === 0) {
                callback();
                return;
            }

            let uploaded = 0;
            const total = selectedFiles.length;

            selectedFiles.forEach(file => {
                window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'))
                    .upload('photos', file,
                        () => {
                            uploaded++;
                            if (uploaded === total) callback();
                        },
                        (error) => {
                            console.error('Upload error:', error);
                            alert('Помилка завантаження фото');
                        }
                    );
            });
        }

        document.querySelectorAll('button[wire\\:click="publish"], button[wire\\:click="saveDraft"]').forEach(
            btn => {
                btn.addEventListener('click', function(e) {
                    if (btn.hasAttribute('wire:loading')) return;

                    e.preventDefault();
                    const isPublish = btn.getAttribute('wire:click') === 'publish';

                    uploadAllFiles(() => {
                        if (isPublish) {
                            Livewire.dispatch('publish');
                        } else {
                            Livewire.dispatch('saveDraft');
                        }
                    });
                });
            });
    });
</script>

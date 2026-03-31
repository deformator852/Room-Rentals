<div class="mx-auto max-w-2xl px-4 py-10">

    <h1 class="text-xl font-semibold text-zinc-900 dark:text-white">Ваш профіль</h1>
    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Оновіть особисту інформацію та аватарку</p>

    @if (session('status'))
        <div
            class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    @if ($emailChanged)
        <div
            class="mt-4 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">
        </div>
    @endif

    <div class="mt-8 space-y-6">

        <div class="flex items-center gap-6">
            <div class="relative">
                @if ($avatar)
                    <img src="{{ $avatar->temporaryUrl() }}"
                        class="h-20 w-20 rounded-full object-cover ring-2 ring-zinc-200 dark:ring-zinc-700" />
                @elseif (Auth::user()->avatar_url)
                    <img src="{{ Storage::url(Auth::user()->avatar_url) }}"
                        class="h-20 w-20 rounded-full object-cover ring-2 ring-zinc-200 dark:ring-zinc-700" />
                @else
                    <div
                        class="flex h-20 w-20 items-center justify-center rounded-full bg-blue-600 text-2xl font-medium text-white">
                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <div>
                <label
                    class="cursor-pointer rounded-lg border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                    Змінити фото
                    <input type="file" wire:model="avatar" class="hidden" accept="image/*" />
                </label>
                <p class="mt-1.5 text-xs text-zinc-400">JPG, PNG до 2MB</p>
                @error('avatar')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="border-t border-zinc-100 dark:border-zinc-800"></div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

            <div>
                <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Ім'я
                </label>
                <input wire:model="first_name" type="text"
                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500" />
                @error('first_name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Прізвище
                </label>
                <input wire:model="last_name" type="text"
                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500" />
                @error('last_name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Email
                </label>
                <input wire:model.live="email" type="email"
                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500" />
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="flex justify-end">
            <button wire:click="save" wire:loading.attr="disabled"
                class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-60 transition-colors">
                <span wire:loading.remove wire:target="save">Зберегти зміни</span>
                <span wire:loading wire:target="save">Збереження...</span>
            </button>
        </div>

    </div>
</div>

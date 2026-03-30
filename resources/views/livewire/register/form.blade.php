<div>
    <div class="bg-zinc-900 rounded-2xl border border-zinc-800 shadow-2xl p-8">
        <form wire:submit="register" class="flex flex-col gap-5">
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label for="first_name" class="text-xs font-medium text-zinc-400 tracking-wide uppercase">Ім'я</label>
                    <input id="first_name" type="text" wire:model="first_name" placeholder="Ім'я" required autofocus
                        autocomplete="given-name"
                        class="w-full bg-zinc-800 border {{ $errors->first('first_name') ? 'border-red-500 focus:ring-red-500/20' : 'border-zinc-700 focus:ring-indigo-500/20' }} rounded-xl px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 outline-none focus:border-indigo-500 focus:ring-4 transition-all duration-150">
                    @error('first_name')
                        <p class="text-xs text-red-400 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="last_name"
                        class="text-xs font-medium text-zinc-400 tracking-wide uppercase">Прізвище</label>
                    <input id="last_name" type="text" wire:model="last_name" placeholder="Прізвище" required
                        autocomplete="family-name"
                        class="w-full bg-zinc-800 border {{ $errors->first('last_name') ? 'border-red-500 focus:ring-red-500/20' : 'border-zinc-700 focus:ring-indigo-500/20' }} rounded-xl px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 outline-none focus:border-indigo-500 focus:ring-4 transition-all duration-150">
                    @error('last_name')
                        <p class="text-xs text-red-400 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="email" class="text-xs font-medium text-zinc-400 tracking-wide uppercase">Електронна
                    пошта</label>
                <input id="email" type="email" wire:model="email" placeholder="email@example.com" required
                    autocomplete="email"
                    class="w-full bg-zinc-800 border {{ $errors->first('email') ? 'border-red-500 focus:ring-red-500/20' : 'border-zinc-700 focus:ring-indigo-500/20' }} rounded-xl px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 outline-none focus:border-indigo-500 focus:ring-4 transition-all duration-150">
                @error('email')
                    <p class="text-xs text-red-400 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="password" class="text-xs font-medium text-zinc-400 tracking-wide uppercase">Пароль</label>
                <div class="relative">
                    <input id="password" type="password" wire:model="password" placeholder="Введіть пароль" required
                        autocomplete="new-password"
                        class="w-full bg-zinc-800 border {{ $errors->first('password') ? 'border-red-500 focus:ring-red-500/20' : 'border-zinc-700 focus:ring-indigo-500/20' }} rounded-xl px-3.5 py-2.5 pr-11 text-sm text-zinc-100 placeholder-zinc-600 outline-none focus:border-indigo-500 focus:ring-4 transition-all duration-150">
                    <button type="button" onclick="togglePassword(this)"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-300 transition-colors">
                        <svg class="eye-on w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="eye-off w-4.5 h-4.5 hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-red-400 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="password_confirmation"
                    class="text-xs font-medium text-zinc-400 tracking-wide uppercase">Підтвердіть пароль</label>
                <div class="relative">
                    <input id="password_confirmation" type="password" wire:model="password_confirmation"
                        placeholder="Повторіть пароль" required autocomplete="new-password"
                        class="w-full bg-zinc-800 border {{ $errors->first('password_confirmation') ? 'border-red-500 focus:ring-red-500/20' : 'border-zinc-700 focus:ring-indigo-500/20' }} rounded-xl px-3.5 py-2.5 pr-11 text-sm text-zinc-100 placeholder-zinc-600 outline-none focus:border-indigo-500 focus:ring-4 transition-all duration-150">
                    <button type="button" onclick="togglePassword(this)"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-300 transition-colors">
                        <svg class="eye-on w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="eye-off w-4.5 h-4.5 hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="text-xs text-red-400 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" data-test="register-user-button"
                class="w-full mt-1 py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 active:scale-[0.98] text-white text-sm font-medium tracking-wide shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all duration-150 outline-none focus:ring-4 focus:ring-indigo-500/30">
                <span wire:loading.remove wire:target="register">Створити акаунт</span>
                <span wire:loading wire:target="register">Завантаження...</span>
            </button>

        </form>
    </div>

    <p class="text-center text-sm text-zinc-600 mt-6">
        Вже маєте акаунт?
        <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 font-medium transition-colors">
            Увійти
        </a>
    </p>

    <script>
        function togglePassword(btn) {
            const input = btn.closest('.relative').querySelector('input');
            input.type = input.type === 'password' ? 'text' : 'password';
            btn.querySelector('.eye-on').classList.toggle('hidden');
            btn.querySelector('.eye-off').classList.toggle('hidden');
        }
    </script>

</div>

</div>

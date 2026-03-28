@extends('layouts.auth')
@section('main')
    <div class="min-h-screen flex items-center justify-center px-4 py-12"
        style="background: radial-gradient(ellipse 60% 50% at 50% -10%, rgba(99,102,241,0.18) 0%, transparent 70%), #09090b;">

        <div class="w-full max-w-md">

            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-600 mb-5 shadow-lg shadow-indigo-500/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </div>
                <h1 class="text-2xl font-semibold text-zinc-100 tracking-tight">Вхід в акаунт</h1>
                <p class="mt-2 text-sm text-zinc-500 font-light">Раді бачити вас знову</p>
            </div>

            @if (session('status'))
                <div
                    class="mb-4 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-sm text-green-400 text-center">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-zinc-900 rounded-2xl border border-zinc-800 shadow-2xl p-8">
                <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
                    @csrf

                    <div class="flex flex-col gap-1.5">
                        <label for="email" class="text-xs font-medium text-zinc-400 tracking-wide uppercase">
                            Електронна пошта
                        </label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                            placeholder="email@example.com" required autofocus autocomplete="email"
                            class="w-full bg-zinc-800 border {{ $errors->first('email') ? 'border-red-500 focus:ring-red-500/20' : 'border-zinc-700 focus:ring-indigo-500/20' }} rounded-xl px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-600 outline-none focus:border-indigo-500 focus:ring-4 transition-all duration-150">
                        @if ($errors->first('email'))
                            <p class="text-xs text-red-400 mt-0.5">{{ $errors->first('email') }}</p>
                        @endif
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="text-xs font-medium text-zinc-400 tracking-wide uppercase">
                                Пароль
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">
                                    Забули пароль?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input id="password" name="password" type="password" placeholder="Введіть пароль" required
                                autocomplete="current-password"
                                class="w-full bg-zinc-800 border {{ $errors->first('password') ? 'border-red-500 focus:ring-red-500/20' : 'border-zinc-700 focus:ring-indigo-500/20' }} rounded-xl px-3.5 py-2.5 pr-11 text-sm text-zinc-100 placeholder-zinc-600 outline-none focus:border-indigo-500 focus:ring-4 transition-all duration-150">
                            <button type="button" onclick="togglePassword(this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-300 transition-colors"
                                aria-label="Показати пароль">
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
                        @if ($errors->first('password'))
                            <p class="text-xs text-red-400 mt-0.5">{{ $errors->first('password') }}</p>
                        @endif
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 rounded-md border-zinc-600 bg-zinc-800 text-indigo-500 accent-indigo-500 cursor-pointer">
                        <span class="text-sm text-zinc-500 group-hover:text-zinc-400 transition-colors">Запам'ятати
                            мене</span>
                    </label>

                    <button type="submit"
                        class="w-full mt-1 py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 active:scale-[0.98] text-white text-sm font-medium tracking-wide shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all duration-150 outline-none focus:ring-4 focus:ring-indigo-500/30">
                        Увійти
                    </button>

                </form>
            </div>

            <p class="text-center text-sm text-zinc-600 mt-6">
                Ще немає акаунту?
                <a href="{{ route('register.index') }}"
                    class="text-indigo-400 hover:text-indigo-300 font-medium transition-colors">
                    Зареєструватися
                </a>
            </p>

        </div>
    </div>

    <script>
        function togglePassword(btn) {
            const input = btn.closest('.relative').querySelector('input');
            input.type = input.type === 'password' ? 'text' : 'password';
            btn.querySelector('.eye-on').classList.toggle('hidden');
            btn.querySelector('.eye-off').classList.toggle('hidden');
        }
    </script>
@endsection

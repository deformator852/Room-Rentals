@extends('layouts.auth')
@section('main')
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-600 mb-5 shadow-lg shadow-indigo-500/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-semibold text-white tracking-tight">Вхід в акаунт</h1>
                <p class="mt-2 text-sm text-zinc-400">Раді бачити вас знову</p>
            </div>

            @if(session('status'))
                <div
                    class="mb-4 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-sm text-green-400 text-center">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-zinc-900 rounded-2xl border border-zinc-800 shadow-2xl p-8">
                <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
                    @csrf

                    <x-input
                        name="email"
                        label="Електронна пошта"
                        value="{{ old('email') }}"
                        type="email"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="email@example.com"
                        :error="$errors->first('email')"
                    />

                    <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-sm font-medium text-zinc-300">Пароль</label>
                            @if(Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">
                                    Забули пароль?
                                </a>
                            @endif
                        </div>
                        <x-password
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Введіть пароль"
                            :error="$errors->first('password')"
                        />
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <x-checkbox name="remember" id="remember" :checked="old('remember')"/>
                        <span class="text-sm text-zinc-400">Запам'ятати мене</span>
                    </label>

                    <x-button
                        type="submit"
                        label="Увійти"
                        primary
                        class="w-full mt-1"
                    />
                </form>
            </div>

            <p class="text-center text-sm text-zinc-500 mt-6">
                Ще немає акаунту?
                <a href="{{ route('register.index') }}"
                   class="text-indigo-400 hover:text-indigo-300 font-medium transition-colors">
                    Зареєструватися
                </a>
            </p>

        </div>
    </div>
@endsection

@extends('layouts.auth')
@section('main')
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            {{-- Заголовок --}}
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-600 mb-5 shadow-lg shadow-indigo-500/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-semibold text-white tracking-tight">Створити акаунт</h1>
                <p class="mt-2 text-sm text-zinc-400">Введіть ваші дані для реєстрації</p>
            </div>

            {{-- Форма --}}
            <div class="bg-zinc-900 rounded-2xl border border-zinc-800 shadow-2xl p-8">
                <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
                    @csrf

                    {{-- Вибір ролі --}}
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-zinc-300">Тип акаунту</label>
                        <div class="grid grid-cols-2 gap-3" x-data="{ role: '{{ old('role', 'tenant') }}' }">

                            <label
                                class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 cursor-pointer transition-all"
                                :class="role === 'tenant'
                                    ? 'border-indigo-500 bg-indigo-500/10'
                                    : 'border-zinc-700 bg-zinc-800 hover:border-zinc-600'"
                            >
                                <input type="radio" name="role" value="tenant" class="sr-only"
                                       x-on:change="role = 'tenant'"
                                    {{ old('role', 'tenant') === 'tenant' ? 'checked' : '' }}/>
                                <svg class="w-7 h-7" :class="role === 'tenant' ? 'text-indigo-400' : 'text-zinc-400'"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-sm font-medium"
                                      :class="role === 'tenant' ? 'text-indigo-300' : 'text-zinc-300'">
                                    Орендар
                                </span>
                                <span class="text-xs text-center"
                                      :class="role === 'tenant' ? 'text-indigo-400/70' : 'text-zinc-500'">
                                    Шукаю житло
                                </span>
                            </label>

                            <label
                                class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 cursor-pointer transition-all"
                                :class="role === 'landlord'
                                    ? 'border-indigo-500 bg-indigo-500/10'
                                    : 'border-zinc-700 bg-zinc-800 hover:border-zinc-600'"
                            >
                                <input type="radio" name="role" value="landlord" class="sr-only"
                                       x-on:change="role = 'landlord'"
                                    {{ old('role') === 'landlord' ? 'checked' : '' }}/>
                                <svg class="w-7 h-7" :class="role === 'landlord' ? 'text-indigo-400' : 'text-zinc-400'"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="text-sm font-medium"
                                      :class="role === 'landlord' ? 'text-indigo-300' : 'text-zinc-300'">
                                    Орендодавець
                                </span>
                                <span class="text-xs text-center"
                                      :class="role === 'landlord' ? 'text-indigo-400/70' : 'text-zinc-500'">
                                    Здаю нерухомість
                                </span>
                            </label>

                        </div>
                        @error('role')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <x-input
                            name="first_name"
                            label="Ім'я"
                            value="{{ old('first_name') }}"
                            type="text"
                            required
                            autofocus
                            autocomplete="given-name"
                            placeholder="Ім'я"
                            :error="$errors->first('first_name')"
                        />
                        <x-input
                            name="last_name"
                            label="Прізвище"
                            value="{{ old('last_name') }}"
                            type="text"
                            required
                            autocomplete="family-name"
                            placeholder="Прізвище"
                            :error="$errors->first('last_name')"
                        />
                    </div>

                    <x-input
                        name="email"
                        label="Електронна пошта"
                        value="{{ old('email') }}"
                        type="email"
                        required
                        autocomplete="email"
                        placeholder="email@example.com"
                        :error="$errors->first('email')"
                    />

                    <x-password
                        name="password"
                        label="Пароль"
                        required
                        autocomplete="new-password"
                        placeholder="Введіть пароль"
                        :error="$errors->first('password')"
                    />

                    <x-password
                        name="password_confirmation"
                        label="Підтвердіть пароль"
                        required
                        autocomplete="new-password"
                        placeholder="Повторіть пароль"
                        :error="$errors->first('password_confirmation')"
                    />

                    <x-button
                        type="submit"
                        label="Створити акаунт"
                        primary
                        class="w-full mt-1"
                        data-test="register-user-button"
                    />
                </form>
            </div>

            <p class="text-center text-sm text-zinc-500 mt-6">
                Вже маєте акаунт?
                <a href="{{ route('login.index') }}"
                   class="text-indigo-400 hover:text-indigo-300 font-medium transition-colors">
                    Увійти
                </a>
            </p>

        </div>
    </div>
@endsection

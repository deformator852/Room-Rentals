@extends('layouts.auth')
@section('main')
    <div class="flex flex-col gap-6">
        <x-auth-header title="Створити акаунт"
                       description="Введіть ваші дані для реєстрації"/>

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

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
                placeholder="Пароль"
                :error="$errors->first('password')"
            />

            <x-password
                name="password_confirmation"
                label="Підтвердіть пароль"
                required
                autocomplete="new-password"
                placeholder="Підтвердіть пароль"
                :error="$errors->first('password_confirmation')"
            />

            <x-button
                type="submit"
                label="Створити акаунт"
                primary
                class="w-full"
                data-test="register-user-button"
            />
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>Вже маєте акаунт?</span>
            <a href="{{ route('login') }}" class="text-primary-500 hover:underline">
                Увійти
            </a>
        </div>
    </div>
@endsection

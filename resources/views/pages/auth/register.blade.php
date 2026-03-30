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
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <h1 class="text-2xl font-semibold text-zinc-100 tracking-tight">Створити акаунт</h1>
                <p class="mt-2 text-sm text-zinc-500 font-light">Введіть ваші дані для реєстрації</p>
            </div>
            <livewire:register.form />
        </div>
    </div>
@endsection('main')

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
            @if (session('error'))
                <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-sm text-red-400 text-center">
                    {{ session('error') }}
                </div>
            @endif
            <livewire:login.form />

        </div>
    </div>
@endsection

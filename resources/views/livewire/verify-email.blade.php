<div class="bg-zinc-900 rounded-2xl border border-zinc-800 shadow-2xl p-8 flex flex-col gap-5">

    <div class="flex flex-col gap-1.5">
        <h2 class="text-white font-semibold text-lg">Підтвердіть пошту</h2>
        <p class="text-sm text-zinc-400">
        </p>
    </div>

    @if ($status)
        <p class="text-sm text-green-400 bg-green-500/10 border border-green-500/20 rounded-xl px-4 py-2.5">
            {{ $status }}
        </p>
    @endif

    <button wire:click="resend"
        class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 active:scale-[0.98] text-white text-sm font-medium tracking-wide shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all duration-150 outline-none focus:ring-4 focus:ring-indigo-500/30">
        <span wire:loading.remove wire:target="resend">Надіслати повторно</span>
        <span wire:loading wire:target="resend">Надсилання...</span>
    </button>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full text-sm text-zinc-500 hover:text-zinc-300 transition-colors">
            Вийти
        </button>
    </form>

</div>

@props(['title', 'description' => null])

<div class="flex flex-col gap-1 text-center">
    <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ $title }}</h1>
    @if($description)
        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
    @endif
</div>

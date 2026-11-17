<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <svg class="h-8 w-9 shrink-0" viewBox="0 0 36 32" fill="none" aria-hidden="true">
        <path d="m1 3 12 26h8L9 3H1Zm15 0 8 18L35 3h-9l-3 6-3-6h-4Z" fill="currentColor" />
    </svg>
    <span class="text-xl font-black uppercase leading-none tracking-[-0.06em]">{{ config('app.name') }}</span>
</span>

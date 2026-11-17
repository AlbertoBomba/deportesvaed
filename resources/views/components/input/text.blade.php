@props([
    'error' => false,
])
<input {{ $attributes->merge([
        'type' => 'text',
        'class' => 'min-h-12 w-full rounded-lg border border-ink/20 bg-paper/50 p-3 text-sm transition-colors focus:border-accent',
        'aria-invalid' => $error ? 'true' : 'false',
    ])->class([
        'border-red-400' => !!$error,
    ]) }}
       maxlength="255">

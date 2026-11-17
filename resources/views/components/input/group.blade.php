@props([
    'label' => null,
    'errors' => [],
    'required' => false,
])

<label {{ $attributes->merge(['class' => 'block space-y-2']) }}>
    <span class="block text-xs font-semibold">
        {{ $label }} @if ($required)
            <small class="text-xs text-red-500">&#42;</small>
        @endif
    </span>

    {{ $slot }}
    @foreach ($errors as $error)
        <p class="my-1 text-sm text-red-500">
            {{ $error }}
        </p>
    @endforeach
</label>

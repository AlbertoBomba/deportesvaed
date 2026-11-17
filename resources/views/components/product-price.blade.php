<span {{ $attributes }}>
    {{ $price?->price->formatted() ?? 'Price unavailable' }}
</span>

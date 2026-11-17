<span {{ $attributes }}>
    {{ $price?->price->formatted() ?? 'Precio no disponible' }}
</span>

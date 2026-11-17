<section>
    <div class="max-w-7xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">
            {{ $this->collection->translateAttribute('name') }}
        </h1>

        <div class="grid grid-cols-1 gap-8 mt-8 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($products as $product)
                <x-product-card :product="$product" />
            @empty
                <p class="col-span-full text-gray-500">No products available in this collection.</p>
            @endforelse
        </div>
        <div class="mt-8">{{ $products->links() }}</div>
    </div>
</section>

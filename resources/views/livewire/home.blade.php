<div>
    <x-welcome-banner />

    <div class="max-w-7xl px-4 py-12 mx-auto space-y-12 sm:px-6 lg:px-8">
        <section>
            <h2 class="text-3xl font-bold">Latest products</h2>
            <div class="grid grid-cols-2 mt-8 lg:grid-cols-4 gap-x-4 gap-y-8">
                @forelse ($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <p class="col-span-full text-gray-500">No products available yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>

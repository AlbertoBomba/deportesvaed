<section>
    <div class="max-w-7xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">
            Products
            @if ($term !== '')
                for <u>{{ $term }}</u>
            @endif
        </h1>

        <div class="grid grid-cols-1 gap-8 mt-8 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($results as $result)
                <x-product-card :product="$result" />
            @empty
                <p class="col-span-full text-gray-500">No products found.</p>
            @endforelse
        </div>
        <div class="mt-8">{{ $results->links() }}</div>
    </div>
</section>

<section class="bg-gray-50">
    <div class="max-w-7xl px-4 py-24 mx-auto text-center sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold sm:text-5xl">{{ config('app.name') }}</h1>
        <p class="mt-4 text-lg text-gray-600">Discover our products and find your next favourite.</p>
        <a class="inline-block px-8 py-3 mt-8 font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700"
           href="{{ route('search.view') }}" wire:navigate>
            Shop all products
        </a>
    </div>
</section>

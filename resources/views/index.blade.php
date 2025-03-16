@extends('layouts.master')

@section('content')
    <main class="my-12">
        <div class="mb-8">
            <h3 class="text-3xl font-semibold">{{ __('All Products') }}</h3>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
            @foreach ($productPaginator as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $productPaginator->links('pagination::tailwind') }}
        </div>
    </main>
@endsection

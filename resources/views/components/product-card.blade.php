<div
    class="border border-gray-200 rounded-lg shadow-sm shadow-gray-50/50 hover:shadow-gray-200/75 transition px-4 py-4 relative z-10">

    <a href="{{ $productUrl }}" class="block text-center">
        <img src="{{ $productImageUrl }}" class="w-full m-auto h-52 object-contain" alt="{{ $productName }}">
    </a>

    @if (!$isProductInStock())
        <span
            class="absolute pointer-events-none top-2 right-2 bg-red-600/65 text-red-50 text-[0.8rem] font-medium rounded px-2 py-0.5">{!! __('Out of Stock') !!}</span>
    @endif

    <div class="mt-4">
        <a href="{{ $productUrl }}" class="hover:underline transition block mb-4">
            <h3 class="font-medium text-sm text-ellipsis line-clamp-2">{{ $productName }}</h3>
        </a>

        <div class="flex items-center justify-between gap-2">
            <span class="font-semibold text-rose-600">{{ $productPrice }}</span>

            <button x-on:click="addToCart({{ $product->id }})" {{ !$isProductInStock() ? 'disabled' : '' }}
                class="btn-add-cart">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
            </button>
        </div>
    </div>
</div>

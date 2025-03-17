<div x-on:keydown.escape.window="isSidebarOpen = false">

    <button x-on:click="isSidebarOpen = true"
        class="flex items-center gap-2 text-gray-700 hover:text-rose-600 text-sm transition">
        <span class="block relative">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>
            <span class="text-xs absolute -top-1 -right-2 bg-rose-600/85 text-white rounded-full px-1.5 py-0.5"
                x-text="cart.total_items"></span>
        </span>
        <span class="hidden md:block">{!! __('Cart') !!} <span class="font-medium"
                x-text="`(${cart.calculation.total})`"></span></span>
    </button>

    <div x-show="isSidebarOpen" x-transition.opacity x-on:click="isSidebarOpen = false"
        class="fixed inset-0 bg-gray-950/65 z-40">
    </div>

    <aside x-cloak :style="{ right: isSidebarOpen ? '0' : '-100%' }"
        class="fixed inset-y-0 w-full sm:w-96 h-full bg-white shadow-xl z-40 transform transition duration-200"
        :class="isSidebarOpen ? 'translate-x-0' : 'translate-x-full'">

        <div x-show="!isCartEmpty()" class="flex flex-col w-full h-full relative z-0"
            :class="{ 'pointer-events-none': isCartLoading }">

            <div x-show="isCartLoading"
                class="absolute inset-0 w-full h-full flex items-center justify-center z-50 bg-white/65">
                <span class="btn-is-loading theme-black"></span>
            </div>

            <div class="flex items-center justify-between px-6 py-4">
                <div class="relative">
                    <h3 class="font-medium text-xl">{{ __('My Cart') }}</h3>
                    <span class="absolute -top-2 -right-4 bg-rose-600/75 text-white rounded-full px-1.5 py-0.5 text-xs"
                        x-text="cart.total_items"></span>
                </div>
                <button x-on:click="isSidebarOpen = false" class="hover:text-rose-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="h-full overflow-auto mx-4 border rounded border-gray-200 px-3">
                <template x-for="cartItem in cart.items" :key="cartItem.id">
                    <div class="grid grid-cols-4 gap-3 border-b last:border-b-0 border-gray-200 py-3 group">
                        <img :src="cartItem.product.product_image_url"
                            class="h-16 w-full object-contain border border-gray-200 rounded-sm p-1"
                            :alt="cartItem.product.name">
                        <div class="col-span-3">
                            <div class="flex gap-0.5 items-start justify-between">
                                <a :href="cartItem.product.product_url" x-text="cartItem.product.name"
                                    class="hover:underline font-medium text-[0.8rem] transition text-ellipsis line-clamp-2"></a>
                                <button x-on:click="removeCartItem(cartItem.id)"
                                    class="hover:text-rose-600 transition opacity-25 group-hover:opacity-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                        class="size-4">
                                        <path
                                            d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z" />
                                    </svg>
                                </button>
                            </div>
                            <div class="flex mt-1 items-center gap-2 justify-between">
                                <span class="text-sm text-gray-600"
                                    x-text="`${cartItem.product.price}x${cartItem.quantity}`"></span>
                                <span class="text-[0.94rem] text-rose-600 font-medium"
                                    x-text="cartItem.subtotal"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="px-4 py-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-800 font-medium">{{ __('Subtotal') }}:</span>
                        <span class="text-sm text-gray-950 font-semibold" x-text="cart.calculation.subtotal"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-800 font-medium">{{ __('Discount') }}:</span>
                        <span class="text-sm text-gray-950 font-semibold"
                            x-text="`-${cart.calculation.discount}`"></span>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-200 pt-1">
                        <span class="text-sm text-gray-950 font-semibold">{{ __('Total') }}:</span>
                        <span class="text-rose-600 font-bold" x-text="cart.calculation.total"></span>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-4">
                    <button x-on:click="clearCart()"
                        class="px-4 py-1.5 font-medium text-sm bg-gray-100 border border-gray-200 text-gray-800 transition hover:border-rose-600 hover:text-rose-600 rounded-lg">{{ __('Clear Cart') }}</button>
                    <button
                        class="px-4 py-1.5 font-medium text-sm bg-rose-600 text-rose-50 rounded-lg transition hover:bg-rose-700">{{ __('Checkout') }}</button>
                </div>
            </div>
        </div>

        <div x-show="isCartEmpty()"
            class="flex flex-col gap-2 text-gray-600 opacity-85 items-center justify-center h-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-12 fill-current" viewBox="0 0 24 24">
                <circle cx="10.5" cy="19.5" r="1.5"></circle>
                <circle cx="17.5" cy="19.5" r="1.5"></circle>
                <path d="m14 13.99 4-5h-3v-4h-2v4h-3l4 5z"></path>
                <path
                    d="M17.31 15h-6.64L6.18 4.23A2 2 0 0 0 4.33 3H2v2h2.33l4.75 11.38A1 1 0 0 0 10 17h8a1 1 0 0 0 .93-.64L21.76 9h-2.14z">
                </path>
            </svg>
            <p class="text-sm">{{ __('Your cart is empty') }}</p>
        </div>

    </aside>

</div>

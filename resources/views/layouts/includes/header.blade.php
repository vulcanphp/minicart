<header class="fixed inset-x-0 top-0 bg-gray-50 shadow-sm w-full h-16 md:h-20 z-40">
    <div class="max-w-6xl h-full mx-auto flex flex-col justify-center items-center">
        <div class="flex w-full px-4">

            <div class="w-5/12 md:w-3/12 flex flex-col justify-center">
                <x-logo class="text-xl md:text-2xl font-light text-gray-600">
                    <span class="hidden sm:block">Mini<span class="font-semibold text-gray-950">Cart</span></span>
                </x-logo>
            </div>

            <div class="w-2/12 md:w-6/12" x-data="searchBox">

                <div x-show="!mobileOpen" class="md:hidden flex items-center justify-center w-full h-full">
                    <button x-on:click="openMobileSearch"
                        class="flex items-center gap-1 text-sm text-gray-600 bg-gray-100 border border-gray-200/75 hover:bg-gray-200/75 hover:text-rose-600 px-3 py-2 rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <span class="hidden sm:block">{{ __('Search') }}</span>
                    </button>
                </div>

                <button x-cloak x-on:click="closeSearchBox" x-show="mobileOpen"
                    class="fixed md:hidden bg-white
                    hover:bg-rose-600 opacity-85 hover:opacity-100 hover:text-rose-50 transition w-7 h-7 rounded-full shadow-md block z-50 top-3 left-1/2
                    inset-x-1/2 transform -translate-x-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 m-auto">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="fixed z-40 top-12 inset-x-5 md:static"
                    :class="{ 'md:block': mobileOpen, 'hidden md:block': !mobileOpen }">
                    <x-search />
                </div>

            </div>

            <div class="w-5/12 md:w-3/12 flex flex-col justify-center items-end">
                <x-cart />
            </div>

        </div>
    </div>
</header>

{{-- Header Spacer --}}
<div class="h-16 md:h-20"></div>

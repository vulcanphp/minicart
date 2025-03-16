<div class="relative z-50" x-data="searchBox" x-on:keydown.escape.window="isOpen = false">

    <form x-on:submit.prevent="fetchResult" class="relative z-50">

        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-5 absolute top-1/2 left-3 transform -translate-y-1/2 text-gray-400">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>

        <input type="text" x-ref="searchInput" x-on:focus="isOpen = true"
            class="px-10 py-2.5 bg-gray-50 focus:bg-white focus:border-rose-600 border-2 border-gray-200 text-gray-950 focus:outline-none placeholder:text-gray-600 rounded-sm w-full text-[0.933rem]"
            placeholder="Search products...">

    </form>

    <div x-cloak x-show="isOpen" x-transition
        class="absolute z-50 top-auto inset-x-0 w-full bg-white shadow-xl rounded-b max-h-96 overflow-y-auto overflow-hidden">

        <div x-show="searchResults.length">
            <template x-for="item in searchResults" :key="item.id">
                <a :href="item.product_url"
                    class="flex focus:bg-gray-50 focus:outline-none justify-between items-center px-4 py-3 hover:bg-gray-50 group">
                    <div class="flex gap-2">
                        <img :src="item.image_url" :alt="item.name" class="w-12 h-12 object-contain rounded-sm">
                        <div>
                            <h5 class="font-medium group-hover:text-rose-700 group-focus:text-rose-700 block"
                                x-text="item.name"></h5>
                            <span class="block text-sm font-medium text-gray-600" x-text="`$${item.price}`"></span>
                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="size-4 text-gray-400 group-hover:text-rose-700 group-focus:text-rose-700">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m19.5 19.5-15-15m0 0v11.25m0-11.25h11.25"></path>
                    </svg>
                </a>
            </template>
        </div>

    </div>

    <div x-cloak x-show="isOpen" x-on:click="isOpen = false" class="fixed inset-0 z-40 bg-gray-950/25"></div>
</div>

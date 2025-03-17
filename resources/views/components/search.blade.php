<div class="relative" x-on:keydown="handleSearchInputKeydown($event)" x-on:keydown.escape.window="isOpen = false"
    x-on:keydown.down.prevent="$focus.wrap().next()" x-on:keydown.up.prevent="$focus.wrap().previous()">

    <form x-on:submit.prevent="fetchResult" class="relative z-20">

        <svg x-on:click="$refs.searchInput.focus()" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor"
            class="size-5 absolute top-1/2 left-3 transform -translate-y-1/2 text-gray-400">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>

        <input type="text" x-ref="searchInput" x-on:keydown.window="focusSearchInput($event)"
            x-on:input.debounce.500ms="fetchResult" x-on:focus="isOpen = true"
            class="px-10 py-2.5 bg-gray-50 focus:bg-white focus:border-rose-600 border-2 border-gray-200 text-gray-950 focus:outline-none placeholder:text-gray-600 rounded-sm w-full text-[0.933rem]"
            placeholder="Search products (Press '/' to focus)">

        <span x-cloak x-show="isLoading"
            class="btn-is-loading theme-black absolute top-1/2 right-6 transform -translate-y-1/2"></span>

    </form>

    <div x-cloak x-show="isOpen" x-transition
        class="absolute z-20 top-auto inset-x-0 w-full bg-white shadow-xl rounded-b max-h-96 overflow-y-auto overflow-hidden">

        <template x-for="item in searchResults" :key="item.id">
            <a :href="item.product_url"
                class="flex focus:bg-gray-50 focus:outline-none justify-between items-center px-4 py-3 hover:bg-gray-50 group focus:border-rose-600 border border-transparent">
                <div class="w-full flex gap-2.5">
                    <img :src="item.image_url" :alt="item.name" class="w-14 h-14 object-contain rounded-sm">
                    <div class="w-full">
                        <h5 class="font-medium group-hover:text-rose-700 group-focus:text-rose-700 text-[0.933rem] text-ellipsis line-clamp-2"
                            x-text="item.name">
                        </h5>
                        <span class="block text-sm font-medium text-gray-600" x-text="item.price"></span>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor"
                    class="size-4 text-gray-400 group-hover:text-rose-700 group-focus:text-rose-700">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 19.5-15-15m0 0v11.25m0-11.25h11.25">
                    </path>
                </svg>
            </a>
        </template>

        <div x-show="query.trim().length && noResults" class="text-center px-8 py-4 text-gray-600 text-sm">
            <p>{{ __('No results for') }}: <span class="font-medium text-gray-900 italic" x-text='`"${query}"`'></span>
            </p>
        </div>

        <template x-if="query.trim().length === 0">
            <div class="px-4 py-4">
                <h3 class="text-[0.933rem] font-semibold text-gray-800">{{ __('Trending Searches') }}</h3>
                <div class="flex flex-wrap gap-x-4 gap-y-2 mt-2">
                    @foreach (['Apple', 'Smartwatch', 'Samsung', 'Shirt'] as $keyword)
                        <button x-on:click="setSearchQuery('{{ $keyword }}')"
                            class="cursor-pointer text-sm font-medium text-gray-600 transition-colors hover:text-rose-600 focus:outline-none focus:text-rose-600 focus:border-rose-600 border border-transparent px-1 py-0.5 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="size-5">
                                <path fill-rule="evenodd"
                                    d="M12.577 4.878a.75.75 0 0 1 .919-.53l4.78 1.281a.75.75 0 0 1 .531.919l-1.281 4.78a.75.75 0 0 1-1.449-.387l.81-3.022a19.407 19.407 0 0 0-5.594 5.203.75.75 0 0 1-1.139.093L7 10.06l-4.72 4.72a.75.75 0 0 1-1.06-1.061l5.25-5.25a.75.75 0 0 1 1.06 0l3.074 3.073a20.923 20.923 0 0 1 5.545-4.931l-3.042-.815a.75.75 0 0 1-.53-.919Z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $keyword }}
                        </button>
                    @endforeach
                </div>
            </div>
        </template>

    </div>

    <div x-cloak x-show="isOpen" x-transition.opacity x-on:click="closeSearchBox()"
        class="fixed inset-0 z-10 bg-gray-950/25"></div>

</div>

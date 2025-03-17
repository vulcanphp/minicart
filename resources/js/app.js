import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

document.addEventListener('alpine:init', () => {

    Alpine.data('miniCart', () => ({
        cart: initialCartSnapshot,
        isSidebarOpen: false,
        isCartLoading: false,
        isCartEmpty() {
            return this.cart.total_items === 0;
        },
        async addToCart(productId) {
            // Add a loading class to the button
            // to indicate that the request is in progress
            this.$el.classList.add('btn-is-loading');

            // Make an AJAX request to add the product to the cart
            this.cartRequest({ product_id: productId, action: 'add', quantity: 1 })
                .then(() => {
                    this.$el.classList.remove('btn-is-loading'); // Remove the loading class
                    this.isSidebarOpen = true;
                });
        },
        removeCartItem(productId) {
            this.cartRequest({ product_id: productId, action: 'remove' });
        },
        clearCart() {
            this.cartRequest({ action: 'clear' })
                .then(() => {
                    this.isSidebarOpen = false;
                });
        },
        async cartRequest(params) {
            this.isCartLoading = true;

            // Make an AJAX request to add the product to the cart
            // and update the cart data
            await axios.post('/cart/ajax', params)
                .then(({ data }) => {
                    this.cart = data.cart; // Update the cart data
                    this.isCartLoading = false;
                });
        },
    }));

    Alpine.data('searchBox', () => ({
        searchResults: [],
        isOpen: false,
        isLoading: false,
        noResults: false,
        query: '',
        fetchResult() {
            if (this.isLoading) return;

            this.query = this.$refs.searchInput.value;
            if (this.query.trim().length === 0) {
                this.searchResults = [];
                this.noResults = false;
                return;
            }

            this.isLoading = true;
            this.noResults = false;

            // Make an AJAX request to fetch search results
            // and update the search results
            axios.post('/search/ajax', { query: this.query })
                .then(({ data }) => {
                    this.searchResults = data;
                    this.noResults = data.length === 0;
                    this.isLoading = false;
                })
        },
        handleSearchInputKeydown(event) {
            // if the user presses backspace or the alpha-numeric keys, focus on the search field
            if ((event.keyCode >= 65 && event.keyCode <= 90) || (event.keyCode >= 48 && event.keyCode <= 57) || event.keyCode === 8) {
                this.$refs.searchInput.focus();
            }
        },
        focusSearchInput(event) {
            if (event.keyCode === 191) {
                event.preventDefault();
                this.$refs.searchInput.focus();
            }
        },
    }));
});


window.Alpine = Alpine;

Alpine.plugin(focus);

Alpine.start();

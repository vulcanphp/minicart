import './bootstrap';

import Alpine from 'alpinejs';

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
        searchResults: [
            {
                id: 1,
                image_url: 'https://tentaz.com/html/elena/assets/images/products/best-product1.png',
                name: 'Latest Ipad with 64gb',
                price: 223.66,
                product_url: '',
            },
            {
                id: 2,
                image_url: 'https://tentaz.com/html/elena/assets/images/products/best-product3.png',
                name: 'Sony Wireless Headphone',
                price: 166.22,
                product_url: '',
            },
        ],
        isOpen: false,

        fetchResult() { }
    }));
});


window.Alpine = Alpine;

Alpine.start();

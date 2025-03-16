import './bootstrap';

import Alpine from 'alpinejs';

document.addEventListener('alpine:init', () => {

    Alpine.data('miniCart', () => ({
        addToCart(productId) {
            this.$el.classList.add('btn-is-loading');
        }
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

<?php

use App\Modules\ShoppingCart;

if (!function_exists('cart')) {
    /**
     * Get the current shopping cart.
     *
     * This function returns an instance of the ShoppingCart module, which encapsulates
     * the items and coupons in the shopping cart and provides methods to manipulate
     * and retrieve the cart data.
     *
     * @return \App\Modules\ShoppingCart
     */
    function cart(): ShoppingCart
    {
        return app(ShoppingCart::class);
    }
}

if (!function_exists('price')) {
    /**
     * Format a price as a string with two decimal places.
     *
     * @param float $price The price to format.
     *
     * @return string The formatted price string.
     */
    function price(float $price): string
    {
        return sprintf('$%.2f', $price);
    }
}


if (!function_exists('get_cart_snapshot')) {
    /**
     * Get a snapshot of the current shopping cart.
     *
     * The snapshot includes the items in the cart, the applied coupons, and the
     * calculation of the total, subtotal, and discount amount.
     *
     * @return array The snapshot of the shopping cart.
     */
    function get_cart_snapshot(): array
    {
        return [
            'items' => collect(cart()->getItems())
                ->map(fn($item) => [
                    'id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'subtotal' => price($item['product']->price * $item['quantity']),
                    'product' => [
                        'name' => $item['product']->name,
                        'price' => price($item['product']->price),
                        'product_url' => $item['product']->getUrl(),
                        'product_image_url' => $item['product']->image_url,
                    ],
                ])
                ->reverse()
                ->values()
                ->all(),
            'total_items' => cart()->getTotalItems(),
            'applied_coupons' => collect(cart()->getCoupons())
                ->map(fn($coupon) => [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'discounted_amount' => price(
                        $coupon->type === 'fixed' ?
                        $coupon->value : cart()->getSubtotal() * ($coupon->value / 100)
                    ),
                ])
                ->reverse()
                ->values()
                ->all(),
            'calculation' => [
                'subtotal' => price(cart()->getSubTotal()),
                'discount' => price(cart()->getDiscount()),
                'total' => price(cart()->getTotal()),
            ],
        ];
    }
}


<?php

use App\Modules\ShoppingCart;

/**
 * Get the current shopping cart.
 *
 * @return \App\Modules\ShoppingCart
 */
if (!function_exists('cart')) {
    function cart(): ShoppingCart
    {
        return app(ShoppingCart::class);
    }
}


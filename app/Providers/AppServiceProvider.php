<?php

namespace App\Providers;

use App\Modules\ShoppingCart;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /**
         * Register the shopping cart service into the container.
         *
         * This is the service which handle the cart data and persist it into the session
         * and provide methods to add, remove, update, and get items from the cart.
         *
         * @see App\Modules\ShoppingCart
         */
        $this->app->singleton(ShoppingCart::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        cart()->addItem(1);
        cart()->addItem(2, 2);
        cart()->addItem(3);

        dd(cart());
    }
}

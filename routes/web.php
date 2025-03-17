<?php

use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'index'])->name('shop');
Route::get('/products/{id}', [ShopController::class, 'show'])->name('shop.show');
Route::post('/cart/ajax', [ShopController::class, 'ajaxCart'])->name('cart.ajax');
Route::post('/search/ajax', [ShopController::class, 'ajaxSearch'])->name('search.ajax');

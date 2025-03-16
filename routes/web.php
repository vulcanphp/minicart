<?php

use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'index'])->name('shop');
Route::get('/products/{id}', [ShopController::class, 'show'])->name('shop.show');

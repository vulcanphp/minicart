<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    dd(Product::all());

    return view('welcome');
});

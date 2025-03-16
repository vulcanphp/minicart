<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $productPaginator = Product::paginate(8);

        return view('index', ['productPaginator' => $productPaginator]);
    }

    public function show(int $id)
    {
        $product = Product::find($id);
        if (empty($product)) {
            abort(404);
        }
        dd($product);
    }
}

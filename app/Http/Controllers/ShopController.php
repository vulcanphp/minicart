<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

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

    public function ajaxCart(Request $request): JsonResponse
    {
        $errors = [];
        try {
            match ($request->post('action')) {
                'add' => cart()->addItem(
                    $request->post('product_id'),
                    $request->post('quantity', 1),
                ),
                'remove' => cart()->removeItem(
                    $request->post('product_id')
                ),
                'clear' => cart()->clearCart(),
                default => $errors[] = 'Unknown action',
            };

            cart()->saveCart();
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
        }

        return response()->json(['errors' => $errors, 'cart' => get_cart_snapshot()]);
    }
}

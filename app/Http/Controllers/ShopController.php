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

        return view('product', ['product' => $product]);
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

    public function ajaxSearch(Request $request): JsonResponse
    {
        $query = trim($request->post('query'));

        $result = [];
        if (!empty($query)) {
            $result = Product::where('name', 'like', "%$query%")
                ->take(8)
                ->map(fn($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image_url' => $product->image_url,
                    'price' => price($product->price),
                    'product_url' => $product->getUrl(),
                ])
                ->all();
        }

        return response()->json($result);
    }
}

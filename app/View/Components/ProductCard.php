<?php

namespace App\View\Components;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductCard extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public Product $product)
    {
    }

    public function productImageUrl(): string
    {
        return $this->product->image_url;
    }

    public function productUrl(): string
    {
        return $this->product->getUrl();
    }

    public function productName(): string
    {
        return $this->product->name;
    }

    public function isProductInStock(): bool
    {
        return $this->product->stock_status === 'in_stock';
    }

    public function productPrice(): string
    {
        return sprintf('$%.2f', $this->product->price);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-card');
    }
}

<?php

namespace App\Modules;

use App\Models\Product;
use Exception;
use Illuminate\Session\SessionManager;

class ShoppingCart
{
    private array $items;
    private array $coupons;
    private bool $changed;

    public function __construct(private SessionManager $sessionManager)
    {
    }

    /**
     * Adds a product to the cart.
     *
     * @param int $id The ID of the product to add.
     * @param int $quantity The quantity of the product to add. Defaults to 1.
     *
     * @throws \Exception If the product does not have enough stock to fulfill
     *                    the request.
     */
    public function addItem(int $id, int $quantity = 1): void
    {
        // Retrieve the product from the database
        $product = $this->productLookup($id);

        if (empty($product)) {
            // Product not found, throw an exception
            throw new Exception(sprintf('Product #%d not found', $id));
        }

        // Check if the product is already in the cart
        // If it is, increment the quantity
        if (isset($this->items[$id])) {
            $quantity = $this->items[$id]['quantity'] + $quantity;
        }

        // Check if the product has enough stock to fulfill the request
        if (!$this->checkStockAvailability($product, $quantity)) {
            // Throw an exception if the product does not have enough stock
            throw new Exception(sprintf('Insufficient stock for product #%d', $id));
        }

        // Add the item to the cart
        $this->items[$id] = ['id' => $id, 'quantity' => $quantity, 'product' => $product];

        // Indicate that the cart has changed
        $this->changed = true;

        // Call the onItemUpdate event
        $this->onItemUpdate($product, $quantity);
    }

    /**
     * Removes a product from the cart.
     *
     * @param int $id The ID of the product to remove.
     */
    public function removeItem(int $id): void
    {
        $product = $this->items[$id]['product'];

        // Remove the item from the cart
        unset($this->items[$id]);

        // Indicate that the cart has changed
        $this->changed = true;

        // Call the onItemUpdate event
        $this->onItemUpdate($product, -1);
    }

    /**
     * Updates the quantity of an item in the cart.
     *
     * @param int $id The ID of the item to update.
     * @param int $quantity The new quantity of the item.
     *
     * @throws \Exception If the product does not have enough stock to fulfill
     *                    the request.
     */
    public function updateItem(int $id, int $quantity): void
    {
        // Retrieve the item from the cart
        $item = $this->getItem($id);

        // Check if the product has enough stock to fulfill the request
        if (!$this->checkStockAvailability($item['product'], $quantity)) {
            // Throw an exception if the product does not have enough stock
            throw new Exception(sprintf('Insufficient stock for product #%d', $id));
        }

        // Update the quantity of the item
        $this->items[$id]['quantity'] = $quantity;

        // Indicate that the cart has changed
        $this->changed = true;

        // Call the onItemUpdate event
        $this->onItemUpdate($item['product'], $quantity);
    }

    /**
     * Clears the cart and resets the state of the cart.
     *
     * This will remove all items from the cart, reset the shipping method, and
     * clear all coupons.
     *
     * @return void
     */
    public function clearCart(): void
    {
        // Indicate that the cart has changed
        $this->changed = true;
        // Reset the items array
        $this->items = [];
        // Reset the added coupons
        $this->coupons = [];
    }

    /**
     * Returns an array of CartItem objects representing all items in the cart.
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Retrieves a single item from the cart by its ID.
     *
     * @param int $id The ID of the item to retrieve.
     *
     * @return array|null The item or null if not found.
     */
    public function getItem(int $id): ?array
    {
        return $this->items[$id] ?? null;
    }

    /**
     * Checks if a coupon is present in the cart.
     *
     * @param string $code The code of the coupon to check.
     * @return bool True if the coupon is in the cart, false otherwise.
     */
    public function hasCoupon(string $code): bool
    {
        return in_array($code, $this->coupons, true);
    }

    /**
     * Adds a coupon to the cart.
     *
     * If the coupon is already in the cart, this method does nothing.
     *
     * @param string $code The coupon code.
     */
    public function addCoupon(string $code): void
    {
        if ($this->hasCoupon($code)) {
            return;
        }

        if (!$this->couponValidate($code)) {
            throw new Exception(sprintf('Invalid coupon code #%s', $code));
        }

        $this->coupons[] = $code;
    }

    /**
     * Returns an array of coupon codes that are currently in the cart.
     *
     * @return array The coupon codes.
     */
    public function getCoupons(): array
    {
        return $this->coupons;
    }

    /**
     * Removes a coupon from the cart.
     *
     * @param string $code The code of the coupon to remove.
     */
    public function removeCoupon(string $code): void
    {
        $this->coupons = collect($this->coupons)
            ->filter(fn($coupon) => $coupon !== $code)
            ->all();
    }

    public function getSubtotal(): float
    {
        return collect($this->items)
            ->map(fn($item) => $item['product']->price * $item['quantity'])
            ->sum();
    }

    public function getTotalItems(): int
    {
        return collect($this->items)
            ->map(fn($item) => $item['quantity'])
            ->sum();
    }

    public function getDiscount(): float
    {
        $amount = 0.00;

        $coupons = $this->couponLookup();

        return $amount;
    }

    /** @internal area */

    protected function checkStockAvailability(Product $product, $quantity): bool
    {
        // This version of code only has a basic check for stock availability
        // using the static product_status is equal to 'in_stock'.
        return $product->stock_status === 'in_stock'; // Check if the product is in stock
    }

    protected function onItemUpdate(Product $product, int $quantity): void
    {

    }

    /**
     * Finds a product by its ID.
     *
     * @param int $id The ID of the product to find.
     *
     * @return \App\Models\Product|null The found product or null if not found.
     */
    protected function productLookup(int $id): ?Product
    {
        return Product::find($id); // This will return a Product object or null
    }

    protected function couponLookup()
    {

    }

    protected function couponValidate()
    {

    }
}

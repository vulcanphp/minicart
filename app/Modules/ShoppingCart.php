<?php

namespace App\Modules;

use App\Models\Coupon;
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
        $this->items = [];
        $this->coupons = [];
        $this->changed = false;
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
        $product = Product::find($id); // It's Okay to use find() here

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
        return isset($this->coupons[$code]);
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

        // Retrieve the coupon with the given code and ensure it is active
        $coupon = Coupon::where('status', '=', 1)
            ->where('code', '=', $code)
            ->first();

        // Return false if the coupon does not exist
        if (!empty($coupon) && $this->isCouponApplicable($coupon)) {
            $this->coupons[$code] = $coupon;
            $this->changed = true;
            return;
        }

        throw new Exception(sprintf('Invalid coupon code #%s', $code));
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
        unset($this->coupons[$code]);
        $this->changed = true;
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

        foreach ($this->coupons as $coupon) {
            if ($coupon->type === 'percentage') {
                $amount += $this->getSubtotal() * ($coupon->value / 100);
            } else {
                $amount += $coupon->value;
            }
        }

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
        // Check if existing coupons are applicable
        foreach ($this->coupons as $coupon) {
            if (!$this->isCouponApplicable($coupon)) {
                $this->removeCoupon($coupon->code);
            }
        }

        // add dynamically/automatically applicable coupons
        $dynamicCoupons = Coupon::where('status', '=', 1)
            ->where('auto_apply', '=', 1)
            ->all();

        foreach ($dynamicCoupons as $coupon) {
            if ($this->hasCoupon($coupon->code) || !$this->isCouponApplicable($coupon)) {
                continue;
            }

            $this->coupons[$coupon->code] = $coupon;
            $this->changed = true;
        }
    }

    /**
     * Checks if a coupon is applicable to the current cart.
     *
     * A coupon is considered applicable if it is active (status = 1) and the
     * conditions associated with the coupon are met. Conditions can include
     * minimum total amount and minimum total items.
     *
     * @param Coupon $coupon The coupon to check.
     * @return bool True if the coupon is applicable, false otherwise.
     */
    protected function isCouponApplicable(Coupon $coupon): bool
    {
        // Check if the coupon has conditions to be met
        if (!empty($coupon->condition)) {

            // Check if the cart's subtotal meets the minimum required by the coupon
            if (isset($coupon->condition['total_amount']) && $coupon->condition['total_amount'] > $this->getSubtotal()) {
                return false;
            }

            // Check if the cart's total items meet the minimum required by the coupon
            if (isset($coupon->condition['total_items']) && $coupon->condition['total_items'] > $this->getTotalItems()) {
                return false;
            }
        }

        // Return true if all conditions are met
        return true;
    }
}

<?php

namespace App\Modules;

use App\Models\Coupon;
use App\Models\Product;
use Exception;
use Illuminate\Session\SessionManager;

/**
 * A module representing the shopping cart.
 *
 * This module encapsulates the items and coupons in the shopping cart and
 * provides methods to manipulate and retrieve the cart data.
 *
 * It also handles the persistence of the cart data in the session.
 *
 * @author Shahin Moysjan <shahin.moyshan2@gmail.com>
 * @package App\Modules
 * @version 1.0
 */
class ShoppingCart
{
    /**
     * The items in the shopping cart.
     *
     * @var array
     */
    private array $items;

    /**
     * The coupons applied to the shopping cart.
     *
     * @var array
     */
    private array $coupons;

    /**
     * Flag indicating whether the cart has been changed.
     *
     * @var bool
     */
    private bool $changed;

    /**
     * Create a new shopping cart instance.
     *
     * This constructor initializes the shopping cart by restoring cart data from the session.
     *
     * @param \Illuminate\Session\SessionManager $sessionManager The session manager instance.
     */
    public function __construct(private SessionManager $sessionManager)
    {
        $this->restoreCart();
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

    /**
     * Calculates the subtotal of all items in the cart.
     *
     * The subtotal is the sum of the price of each item in the cart,
     * multiplied by the quantity of each item.
     *
     * @return float The subtotal of all items in the cart.
     */
    public function getSubtotal(): float
    {
        return collect($this->items)
            ->map(fn($item) => $item['product']->price * $item['quantity'])
            ->sum();
    }

    /**
     * Returns the total number of items in the cart.
     *
     * This value is simply the sum of the quantity of each item in the cart.
     *
     * @return int The total number of items in the cart.
     */
    public function getTotalItems(): int
    {
        return collect($this->items)
            ->map(fn($item) => $item['quantity'])
            ->sum();
    }

    /**
     * Calculates the total discount of all coupons in the cart.
     *
     * If a coupon is a percentage-based discount, the discount amount is
     * calculated as a percentage of the subtotal. If a coupon is a fixed
     * amount discount, the discount amount is the value of the coupon.
     *
     * @return float The total discount amount of all coupons in the cart.
     */
    public function getDiscount(): float
    {
        $amount = 0.00;

        // Loop through all coupons in the cart
        foreach ($this->coupons as $coupon) {
            if ($coupon->type === 'percentage') {
                // Calculate the discount amount as a percentage of the subtotal
                $amount += $this->getSubtotal() * ($coupon->value / 100);
            } else {
                // Calculate the discount amount as a fixed amount
                $amount += $coupon->value;
            }
        }

        return $amount;
    }

    /**
     * Calculates the total cost of all items in the cart, minus any discounts.
     *
     * This value is the subtotal of all items in the cart, minus the total discount
     * of all coupons in the cart.
     *
     * @return float The total cost of all items in the cart, minus any discounts.
     */
    public function getTotal(): float
    {
        return $this->getSubtotal() - $this->getDiscount();
    }

    /**
     * Restore the cart data from the session.
     *
     * This method is called when the shopping cart instance is created.
     * It loads the cart data from the session and attempts to re-add all items
     * and coupons to the cart. If any error occurs during the re-add process,
     * the method sets the 'changed' flag to true.
     *
     * @return void
     */
    public function restoreCart(): void
    {
        $this->items = [];
        $this->coupons = [];

        $changedApplied = false;
        $snapshot = $this->sessionManager->get('cart', []);

        if (isset($snapshot['items'])) {
            // Re-add all items in the cart
            foreach ($snapshot['items'] as $item) {
                try {
                    $this->addItem($item['product_id'], $item['quantity']);
                } catch (Exception $e) {
                    $changedApplied = true;
                }
            }

            // Re-add all coupons in the cart
            foreach (($snapshot['coupons'] ?? []) as $coupon) {
                try {
                    $this->addCoupon($coupon);
                } catch (Exception $e) {
                    $changedApplied = true;
                }
            }
        }

        // Set the 'changed' flag to true if any error occurred during the re-add process
        $this->changed = $changedApplied;
    }

    /**
     * Stores the current state of the cart in the session.
     *
     * This method is called when the shopping cart instance is destroyed.
     * It takes a snapshot of the cart's current state and stores it in the
     * session so that it can be restored later.
     *
     * @return void
     */
    public function saveCart()
    {
        // Skip if the cart has not changed
        if (!$this->changed) {
            return;
        }

        // Create a snapshot of the current cart state
        $snapshot = [
            // Store the items in the cart
            'items' => collect($this->items)
                ->map(fn($item) => [
                    // Store only the product ID and quantity
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity']
                ])
                ->all(),
            // Store the coupons in the cart
            'coupons' => array_keys($this->coupons)
        ];

        // Store the snapshot in the session
        $this->sessionManager->put('cart', $snapshot);
    }

    /** @internal area */

    /**
     * Checks if a product is in stock in the desired quantity.
     *
     * Currently only checks if the product is marked as in stock,
     * but could be extended to also check the actual stock level.
     *
     * @param  \App\Models\Product  $product
     * @param  int  $quantity
     * @return bool
     */
    protected function checkStockAvailability(Product $product, $quantity): bool
    {
        // This version of code only has a basic check for stock availability
        // using the static product_status is equal to 'in_stock'.
        return $product->stock_status === 'in_stock'; // Check if the product is in stock
    }

    /**
     * Triggers the onItemUpdate event when an item is added or updated in the cart.
     *
     * This method checks if existing coupons are applicable after the item is added
     * or updated. If a coupon is no longer applicable, it is removed from the
     * cart. Additionally, it checks for any dynamically/automatically applicable
     * coupons and adds them to the cart if they are applicable.
     *
     * @param  \App\Models\Product  $product  The product that was added or updated.
     * @param  int  $quantity  The quantity of the product that was added or updated.
     * @return void
     */
    protected function onItemUpdate(Product $product, int $quantity): void
    {
        // Check if existing coupons are still applicable
        foreach ($this->coupons as $coupon) {
            if (!$this->isCouponApplicable($coupon)) {
                // Remove the coupon if it is no longer applicable
                $this->removeCoupon($coupon->code);
            }
        }

        // Check for dynamically/automatically applicable coupons
        $dynamicCoupons = Coupon::where('status', '=', 1)
            ->where('auto_apply', '=', 1)
            ->all();

        foreach ($dynamicCoupons as $coupon) {
            if ($this->hasCoupon($coupon->code) || !$this->isCouponApplicable($coupon)) {
                // Skip if the coupon is already in the cart or if it is not applicable
                continue;
            }

            // Add the coupon to the cart
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

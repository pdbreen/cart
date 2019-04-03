<?php

declare(strict_types=1);

namespace PDB\PhpCart\Interfaces;

use PDB\PhpCart\Address;
use PDB\PhpCart\Cart;
use PDB\PhpCart\CartItem;

interface CartInterface
{
    // Define the interface we will share across projects

    /**
     * Return number of line items in cart (NOT total qty)
     *
     * @return int
     */
    public function itemCount(): int;

    /**
     * Return items in cart as iterable
     *
     * @return int
     */
    public function items(): iterable;

    /**
     * Return CartItem if exists in cart, null otherwise
     *
     * @param string $sku
     * @return CartItem|null
     */
    public function findItem(string $sku): ?CartItem;

    /**
     * Add an item to a cart
     *
     * @param string $sku
     * @param int $qty
     * @return Cart
     */
    public function addItem(string $sku, int $qty): Cart;

    /**
     * Remove item from a cart
     *
     * @param string $sku
     * @return Cart
     */
    public function removeItem(string $sku): Cart;

    /**
     * Remove all items from a cart
     *
     * @return Cart
     */
    public function clearCart(): Cart;

    /**
     * Total value of items in cart
     * INCLUDES all item discounts
     * EXCLUDES tax, shipping, handling, order level discounts
     *
     * @return float
     */
    public function itemTotal(): float;

    /**
     * Tax for all items in cart
     *
     * @return float
     */
    public function tax(): float;

    /**
     * Final total - includes all fees and all discounts
     *
     * @return float
     */
    public function total(): float;

    /**
     * Return shipping address
     *
     * @return Address|null
     */
    public function shipAddress(): ?Address;

    /**
     * Set or clear ship address
     *
     * @param Address|null $address
     * @return Cart
     */
    public function setShipAddress(?Address $address = null): Cart;
}

<?php

declare(strict_types=1);

namespace PDB\PhpCart;

use PDB\PhpCart\Exception\LogicException;
use PDB\PhpCart\Interfaces\CartInterface;
use PDB\PhpCart\Pipeline\Pipeline;

class Cart implements CartInterface
{
    /** @var array */
    private $cartItems;

    /** @var Address|null */
    private $address;

    public function __construct()
    {
        $this->cartItems = [];
        $this->address = null;
    }

    /**
     * Return number of line items in cart (NOT total qty)
     *
     * @return int
     */
    public function itemCount(): int
    {
        return count($this->cartItems);
    }

    /**
     * Return items in cart as iterable
     *
     * @return int
     */
    public function items(): iterable
    {
        // TODO - decide if better to return keyed array or items alone
        return array_values($this->cartItems);
    }

    /**
     * Return CartItem if exists in cart, null otherwise
     *
     * @param string $sku
     * @return CartItem|null
     */
    public function findItem(string $sku): ?CartItem
    {
        return $this->cartItems[$sku] ?? null;
    }


    /**
     * Add an item to a cart
     *
     * @param string $sku
     * @param int $qty
     * @return Cart
     */
    public function addItem(string $sku, int $qty): Cart
    {
        // Validate inputs
        if ($qty <= 0) {
            throw new LogicException("Invalid item qty: {$qty}");
        }

        // Check if item is in cart
        if ($item = $this->findItem($sku)) {
            // NOTE:  We treat input qty as net addition to existing qty
            $item->increaseQty($qty);
        } else {
            // New item for cart
            $this->cartItems[$sku] = new CartItem($sku, $qty);
        }

        // Run pipeline after change
        return (new Pipeline())->run($this);
    }

    /**
     * Remove item from a cart
     *
     * @param string $sku
     * @return Cart
     */
    public function removeItem(string $sku): Cart
    {
        if (!$this->findItem($sku)) {
            throw new LogicException("Sku not found in cart: {$sku}");
        }

        // Removal is easy, remove key
        unset($this->cartItems[$sku]);

        // Run pipeline after change
        return (new Pipeline())->run($this);
    }

    /**
     * Remove all items from a cart
     *
     * @return Cart
     */
    public function clearCart(): Cart
    {
        // Reset all items
        $this->cartItems = [];

        // Run pipeline after change
        return (new Pipeline())->run($this);
    }

    /**
     * Total value of items in cart
     * INCLUDES all item discounts
     * EXCLUDES tax, shipping, handling, order level discounts
     *
     * @return float
     */
    public function itemTotal(): float
    {
        $itemTotal = 0;
        foreach ($this->cartItems as $item) {
            /** @var CartItem $item */
            $itemTotal += $item->extendedPrice();
        }
        return $itemTotal;
    }

    /**
     * Tax for all items in cart
     *
     * @return float
     */
    public function tax(): float
    {
        $tax = 0;
        foreach ($this->cartItems as $item) {
            /** @var CartItem $item */
            $tax += $item->tax();
        }
        return $tax;
    }

    /**
     * Final total - includes all fees and all discounts
     *
     * @return float
     */
    public function total(): float
    {
        // TODO - add in other fees or credits such as shipping, handling, order level discounts
        return $this->itemTotal() + $this->tax();
    }

    /**
     * Return shipping address
     *
     * @return Address|null
     */
    public function shipAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * Set or clear ship address
     *
     * @param Address|null $address
     * @return Cart
     */
    public function setShipAddress(?Address $address = null): Cart
    {
        $this->address = $address;

        // Run pipeline after change
        return (new Pipeline())->run($this);
    }
}

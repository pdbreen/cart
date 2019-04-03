<?php

declare(strict_types=1);

namespace PDB\PhpCart\Interfaces;

use PDB\PhpCart\Cart;
use PDB\PhpCart\CartItem;

interface CartItemInterface
{
    // Define the interface we will share across projects

    /**
     * Create a new cart item
     *
     * @param string $sku
     * @param int $qty
     */
    public function __construct(string $sku, int $qty);

    /**
     * Return product ID
     *
     * @return string
     */
    public function sku(): string;

    /**
     * Return item quantity
     *
     * @return int
     */
    public function qty(): int;

    /**
     * Return per item list price
     *
     * @return float
     */
    public function listPrice(): float;

    /**
     * Return (qty * listPrice)
     *
     * @return float
     */
    public function extendedListPrice(): float;

    /**
     * Return total line discount
     *
     * @return float
     */
    public function discount(): float;

    /**
     * Establish total line discount
     *
     * @param float $discount
     * @return CartItem
     */
    public function setDiscount(float $discount): CartItem;

    /**
     * Return extended price = ((qty * price) - discount)
     *
     * @return float
     */
    public function extendedPrice(): float;

    /**
     * Return item tax amount
     *
     * @return float
     */
    public function tax(): float;

    /**
     * Establish item tax
     *
     * @param float $tax
     * @return CartItem
     */
    public function setTax(float $tax): CartItem;

    /**
     * Increase item quantity
     *
     * @param int $qty
     * @return CartItem
     */
    public function increaseQty(int $qty): CartItem;

    /**
     * Decrease item quantity
     *
     * @param int $qty
     * @return CartItem
     */
    public function decreaseQty(int $qty): CartItem;
}

<?php

declare(strict_types=1);

namespace PDB\PhpCart;

use PDB\PhpCart\Exception\LogicException;
use PDB\PhpCart\Interfaces\CartItemInterface;
use PDB\PhpCart\Services\CatalogService;

class CartItem implements CartItemInterface
{
    /** @var string */
    private $sku;
    /** @var float */
    private $listPrice;
    /** @var int */
    private $qty;
    /** @var float */
    private $discount;
    /** @var float */
    private $tax;

    /**
     * Create a new cart item
     *
     * @param string $sku
     * @param int $qty
     */
    public function __construct(string $sku, int $qty)
    {
        if ($qty <= 0) {
            throw new LogicException("Invalid item qty: {$qty}");
        }

        $price = CatalogService::findPrice($sku);
        if (empty($price)) {
            throw new LogicException("Invalid product SKU: {$sku}");
        }

        $this->sku = $sku;
        $this->listPrice = $price;
        $this->qty = $qty;
        $this->discount = 0;
        $this->tax = 0;
    }

    /**
     * Return product ID
     *
     * @return string
     */
    public function sku(): string
    {
        return $this->sku;
    }

    /**
     * Return item quantity
     *
     * @return int
     */
    public function qty(): int
    {
        return $this->qty;
    }

    /**
     * Return per item list price
     *
     * @return float
     */
    public function listPrice(): float
    {
        return $this->listPrice;
    }

    /**
     * Return (qty * listPrice)
     *
     * @return float
     */
    public function extendedListPrice(): float
    {
        return round($this->qty * $this->listPrice, 2);
    }

    /**
     * Return total line discount
     *
     * @return float
     */
    public function discount(): float
    {
        return $this->discount;
    }

    /**
     * Establish total line discount
     *
     * @param float $discount
     * @return CartItem
     */
    public function setDiscount(float $discount): CartItem
    {
        if ($discount < 0) {
            throw new LogicException("Discount must be 0 or greater: {$discount}");
        }
        if ($discount > $this->extendedListPrice()) {
            throw new LogicException("Discount cannot exceed total item price: {$discount} > {$this->extendedListPrice()}");
        }
        $this->discount = round($discount, 2);
        return $this;
    }

    /**
     * Return extended price = ((qty * price) - discount)
     *
     * @return float
     */
    public function extendedPrice(): float
    {
        return round((($this->qty * $this->listPrice) - $this->discount), 2);
    }

    /**
     * Return item tax amount
     *
     * @return float
     */
    public function tax(): float
    {
        return $this->tax;
    }

    /**
     * Establish item tax
     *
     * @param float $tax
     * @return CartItem
     */
    public function setTax(float $tax): CartItem
    {
        if ($tax < 0) {
            throw new LogicException("Tax must be 0 or greater: {$tax}");
        }

        $this->tax = round($tax, 2);
        return $this;
    }

    /**
     * Increase item quantity
     *
     * @param int $qty
     * @return CartItem
     */
    public function increaseQty(int $qty): CartItem
    {
        if ($qty <= 0) {
            throw new LogicException("Qty adjustment must be positive: {$qty}");
        }

        $this->qty += $qty;
        return $this;
    }

    /**
     * Decrease item quantity
     *
     * @param int $qty
     * @return CartItem
     */
    public function decreaseQty(int $qty): CartItem
    {
        if ($qty <= 0) {
            throw new LogicException("Qty adjustment must be positive: {$qty}");
        }

        // Dont allow qty to equal or exceed current qty
        if ($qty >= $this->qty) {
            throw new LogicException("Qty decrease cannot exceed current qty: {$qty} >= {$this->qty}");
        }

        $this->qty -= $qty;
        return $this;
    }
}

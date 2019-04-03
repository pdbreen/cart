<?php


namespace PDB\PhpCart\Services;


use PDB\PhpCart\CartItem;

class DiscountService
{

    /** @var DiscountService|null */
    static private $instance = null;

    /** @var array  */
    private $discounts;

    private function __construct()
    {
        $this->discounts = [];
    }

    /**
     * Singleton accessor
     * @return DiscountService
     */
    static private function instance(): DiscountService
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Push discount into service
     *
     * @param Discount $discount
     */
    static public function addDiscount(Discount $discount)
    {
        self::instance()->discounts[] = $discount;;
    }

    /**
     * Determine max discount for a given cart item
     *
     * @param CartItem $item
     * @return float
     */
    static public function findMaxDiscount(CartItem $item): float
    {
        $maxAmt = 0;
        foreach (self::instance()->discounts as $discount) {
            /** @var Discount $discount */
            $amt = $discount->discountValue($item);
            if ($amt > $maxAmt) {
                $maxAmt = $amt;
            }
        }

        return $maxAmt;
    }

    /**
     * Reset service
     */
    static public function reset()
    {
        self::instance()->discounts = [];
    }
}
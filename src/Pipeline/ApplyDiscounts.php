<?php

declare(strict_types=1);

namespace PDB\PhpCart\Pipeline;

use PDB\PhpCart\Cart;
use PDB\PhpCart\CartItem;
use PDB\PhpCart\Services\DiscountService;

class ApplyDiscounts extends Step
{
    public function apply(Cart $cart): Cart
    {
        // Determine item tax ability
        foreach ($cart->items() as $item) {
            /** @var CartItem $item */
            $discount = DiscountService::findMaxDiscount($item);
            $item->setDiscount($discount);
        }

        returN $cart;
    }

}

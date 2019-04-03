<?php

declare(strict_types=1);

namespace PDB\PhpCart\Pipeline;

use PDB\PhpCart\Cart;
use PDB\PhpCart\CartItem;
use PDB\PhpCart\Services\TaxService;

class CalculateTax extends Step
{
    public function apply(Cart $cart): Cart
    {
        // Determine destination
        $address = $cart->shipAddress();
        $state = $address ? $address->stateOrProvince : null;

        // Determine item tax ability
        foreach ($cart->items() as $item) {
            /** @var CartItem $item */
            $itemRate = TaxService::findTaxRate($item->sku(), $state);

            // NOTE:  Tax is calculated on extended price
            $item->setTax($itemRate * $item->extendedPrice());
        }

        returN $cart;
    }
}

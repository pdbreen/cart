<?php

declare(strict_types=1);

namespace PDB\PhpCart\Pipeline;

use PDB\PhpCart\Cart;

class Pipeline
{
    // Pipeline steps that will be applied to Cart
    // NOTE:  Order is important!  Ex, discounts must be calculated before totals
    private $steps = [
        ApplyDiscounts::class,
        CalculateTax::class,
    ];

    public function run(Cart $cart): Cart
    {
        // Run the pipeline
        foreach ($this->steps as $step) {
            // Instantiate and run each step
            (new $step())->apply($cart);
        }

        return $cart;
    }
}

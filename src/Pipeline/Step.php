<?php

declare(strict_types=1);

namespace PDB\PhpCart\Pipeline;

use PDB\PhpCart\Cart;

abstract class Step
{
    // We must be able to apply each step
    abstract public function apply(Cart $cart): Cart;
}

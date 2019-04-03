<?php


namespace PDB\PhpCart\Services;


use PDB\PhpCart\CartItem;

class Discount
{
    const TYPE_PERCENT_OFF = 'PERCENT';
    const TYPE_DOLLAR_OFF = 'DOLLAR';
    const TYPE_BOGO = 'BOGO';

    /** @var string */
    public $type;

    /** @var string */
    public $targetSku;

    /** @var float */
    public $value;

    public function __construct(string $type, string $sku, float $value = 0)
    {
        $this->type = $type;
        $this->targetSku = $sku;
        $this->value = $value;
    }

    public function discountValue(CartItem $item): float
    {
        if ($item->sku() === $this->targetSku) {
            if ($this->type === self::TYPE_PERCENT_OFF) {
                // Discount is function of extended list price
                return round($item->extendedListPrice() * $this->value, 2);
            }

            if ($this->type === self::TYPE_DOLLAR_OFF) {
                // Discount is function of qty
                return round($item->qty() * $this->value, 2);
            }

            if ($this->type === self::TYPE_BOGO) {
                // Discount is function of number of paired items
                $pairs = floor($item->qty() / 2);
                return round($pairs * $item->listPrice(), 2);
            }
        }

        return 0;
    }
}
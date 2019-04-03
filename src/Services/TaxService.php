<?php


namespace PDB\PhpCart\Services;


class TaxService
{
    /** @var TaxService|null */
    static private $instance = null;

    /** @var array  */
    private $taxTable;

    private function __construct()
    {
        $this->taxTable = [];
    }

    /**
     * Singleton accessor
     * @return TaxService
     */
    static private function instance(): TaxService
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Establish tax rate existence in catalog
     * NOTE:  NULL state establishes default rate
     * TODO:  change to use TaxRate model (out of scope)
     *
     * @param string $sku
     * @param float $rate
     * @param string|null $state
     */
    static public function addTaxRate(string $sku, float $rate, ?string $state = null)
    {
        self::instance()->taxTable[self::rateKey($sku, $state)] = $rate;
    }

    /**
     * Find tax rate for product/geo instance
     * TODO:  change to use TaxRate model (out of scope)
     *
     * @param string $sku
     * @param string|null $state
     * @return float
     */
    static public function findTaxRate(string $sku, ?string $state = null): float
    {
        // Find rate with given state
        $rate = self::instance()->taxTable[self::rateKey($sku, $state)] ?? null;

        // If state was supplied but no rate found, return default rate (if any)
        if ($state && is_null($rate)) {
            return self::findTaxRate($sku, null);
        }

        // Return found rate or 0 if no defalt
        return $rate ?? 0;
    }

    /**
     * Create unique key for sku + geo
     * @param string $sku
     * @param string|null $state
     * @return string
     */
    static private function rateKey(string $sku, ?string $state = null)
    {
        return $sku . ':' . ($state ?? '');
    }

    /**
     * Reset service
     */
    static public function reset()
    {
        self::instance()->taxTable = [];
    }
}
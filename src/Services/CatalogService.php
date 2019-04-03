<?php


namespace PDB\PhpCart\Services;


class CatalogService
{
    /** @var CatalogService|null */
    static private $instance = null;

    /** @var array  */
    private $products;

    private function __construct()
    {
        $this->products = [];
    }

    /**
     * Singleton accessor
     * @return CatalogService
     */
    static private function instance(): CatalogService
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Establish product existence in catalog
     * TODO:  change to use Product model (out of scope)
     *
     * @param string $sku
     * @param float $price
     */
    static public function addProduct(string $sku, float $price)
    {
        self::instance()->products[$sku] = $price;
    }

    /**
     * Find Product instance
     * TODO:  change to use Product model (out of scope)
     *
     * @param string $sku
     * @return float|null
     */
    static public function findPrice(string $sku): ?float
    {
        return self::instance()->products[$sku] ?? null;
    }

    /**
     * Reset service
     */
    static public function reset()
    {
        self::instance()->products = [];
    }
}
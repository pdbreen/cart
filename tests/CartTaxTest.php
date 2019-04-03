<?php

declare(strict_types=1);

namespace PDB\PhpCart;

use PDB\PhpCart\Services\CatalogService;
use PDB\PhpCart\Services\DiscountService;
use PDB\PhpCart\Services\TaxService;
use PHPUnit\Framework\TestCase;

class CartTaxTest extends TestCase
{
    const TEST_TAXABLE_SKU = 'TEST1';
    const TEST_TAXABLE_SKU_PRICE = 3.25;

    const TEST_NON_TAXABLE_SKU = 'TEST2';
    const TEST_NON_TAXABLE_SKU_PRICE = 6.45;

    const TEST_TAXABLE_STATE = 'MA';
    const TEST_NON_TAXABLE_STATE = 'NH';

    const TAX_RATE_DEFAULT = 0.05;
    const TAX_RATE_NH = 0.0;
    const TAX_RATE_MA = 0.10;

    public function setUp()
    {
        parent::setUp();
        // Setup catalog and tax tables
        CatalogService::addProduct(self::TEST_TAXABLE_SKU, self::TEST_TAXABLE_SKU_PRICE);
        CatalogService::addProduct(self::TEST_NON_TAXABLE_SKU, self::TEST_NON_TAXABLE_SKU_PRICE);

        // Establish default rate
        TaxService::addTaxRate(self::TEST_TAXABLE_SKU, self::TAX_RATE_DEFAULT);
        // Establish geo rates
        TaxService::addTaxRate(self::TEST_TAXABLE_SKU, self::TAX_RATE_NH, self::TEST_NON_TAXABLE_STATE);
        TaxService::addTaxRate(self::TEST_TAXABLE_SKU, self::TAX_RATE_MA, self::TEST_TAXABLE_STATE);
    }

    public function tearDown()
    {
        parent::tearDown();
        CatalogService::reset();
        DiscountService::reset();
        TaxService::reset();
    }

    public function testCanTaxEmptyCart() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);
        $this->assertEquals(0, $cart->itemCount());
        $this->assertEquals(0, $cart->tax());
    }

    public function testCanTaxCartItems() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);

        $cart->addItem(self::TEST_TAXABLE_SKU, 1);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertEquals(round(self::TEST_TAXABLE_SKU_PRICE * self::TAX_RATE_DEFAULT, 2), $cart->tax());

        $cart->addItem(self::TEST_TAXABLE_SKU, 2);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertEquals(round((3 * self::TEST_TAXABLE_SKU_PRICE) * self::TAX_RATE_DEFAULT, 2), $cart->tax());
    }

    public function testCanTaxMixedCartItems() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);

        $cart->addItem(self::TEST_NON_TAXABLE_SKU, 1);

        $cart->addItem(self::TEST_TAXABLE_SKU, 1);
        $this->assertEquals(2, $cart->itemCount());
        $this->assertEquals(round(self::TEST_TAXABLE_SKU_PRICE * self::TAX_RATE_DEFAULT, 2), $cart->tax());

        $cart->addItem(self::TEST_TAXABLE_SKU, 2);
        $this->assertEquals(2, $cart->itemCount());
        $this->assertEquals(round((3 * self::TEST_TAXABLE_SKU_PRICE) * self::TAX_RATE_DEFAULT, 2), $cart->tax());
    }

    public function testCanTaxCartItemsByTaxableGeography() : void
    {
        $address = new Address();
        $address->stateOrProvince = 'MA';

        $cart = new Cart();
        $this->assertNotNull($cart);
        $cart->setShipAddress($address);

        $cart->addItem(self::TEST_TAXABLE_SKU, 1);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertEquals(round(self::TEST_TAXABLE_SKU_PRICE * self::TAX_RATE_MA, 2), $cart->tax());

        $cart->addItem(self::TEST_TAXABLE_SKU, 2);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertEquals(round((3 * self::TEST_TAXABLE_SKU_PRICE) * self::TAX_RATE_MA, 2), $cart->tax());

        $address->stateOrProvince = 'CA';
        $cart->setShipAddress($address);
        $this->assertEquals(round((3 * self::TEST_TAXABLE_SKU_PRICE) * self::TAX_RATE_DEFAULT, 2), $cart->tax());
    }

    public function testCanTaxCartItemsByNonTaxableGeography() : void
    {
        $address = new Address();
        $address->stateOrProvince = 'NH';

        $cart = new Cart();
        $this->assertNotNull($cart);
        $cart->setShipAddress($address);

        $cart->addItem(self::TEST_TAXABLE_SKU, 1);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertEquals(0, $cart->tax());

        $cart->addItem(self::TEST_TAXABLE_SKU, 2);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertEquals(0, $cart->tax());

        $address->stateOrProvince = 'CA';
        $cart->setShipAddress($address);
        $this->assertEquals(round((3 * self::TEST_TAXABLE_SKU_PRICE) * self::TAX_RATE_DEFAULT, 2), $cart->tax());
    }
}

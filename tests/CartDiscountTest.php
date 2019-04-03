<?php

declare(strict_types=1);

namespace PDB\PhpCart;

use PDB\PhpCart\Services\CatalogService;
use PDB\PhpCart\Services\Discount;
use PDB\PhpCart\Services\DiscountService;
use PDB\PhpCart\Services\TaxService;
use PHPUnit\Framework\TestCase;

class CartDiscountTest extends TestCase
{
    const TEST_SKU = 'NODISCOUNT';
    const TEST_SKU_PRICE = 5.75;

    const TEST_PCT_DISCOUNT_SKU = 'TEST1';
    const TEST_PCT_DISCOUNT_SKU_PRICE = 4.00;
    const TEST_PCT_DISCOUNT = 0.10;

    const TEST_DOLLAR_DISCOUNT_SKU = 'TEST2';
    const TEST_DOLLAR_DISCOUNT_SKU_PRICE = 3.25;
    const TEST_DOLLAR_DISCOUNT = 1.25;

    const TEST_BOGO_DISCOUNT_SKU = 'TEST3';
    const TEST_BOGO_DISCOUNT_SKU_PRICE = 6.50;

    public function setUp()
    {
        parent::setUp();
        // Setup catalog and tax tables
        CatalogService::addProduct(self::TEST_SKU, self::TEST_SKU_PRICE);
        CatalogService::addProduct(self::TEST_PCT_DISCOUNT_SKU, self::TEST_PCT_DISCOUNT_SKU_PRICE);
        CatalogService::addProduct(self::TEST_DOLLAR_DISCOUNT_SKU, self::TEST_DOLLAR_DISCOUNT_SKU_PRICE);
        CatalogService::addProduct(self::TEST_BOGO_DISCOUNT_SKU, self::TEST_BOGO_DISCOUNT_SKU_PRICE);

        // Establish discounts
        DiscountService::addDiscount(
            new Discount(Discount::TYPE_PERCENT_OFF, self::TEST_PCT_DISCOUNT_SKU, self::TEST_PCT_DISCOUNT)
        );

        DiscountService::addDiscount(
            new Discount(Discount::TYPE_DOLLAR_OFF, self::TEST_DOLLAR_DISCOUNT_SKU, self::TEST_DOLLAR_DISCOUNT)
        );

        DiscountService::addDiscount(
            new Discount(Discount::TYPE_BOGO, self::TEST_BOGO_DISCOUNT_SKU, self::TEST_BOGO_DISCOUNT_SKU_PRICE)
        );
    }

    public function tearDown()
    {
        parent::tearDown();
        CatalogService::reset();
        DiscountService::reset();
        TaxService::reset();
    }

    public function testDiscountEmptyCart() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);
        $this->assertEquals(0, $cart->itemCount());
        $this->assertEquals(0, $cart->total());
    }

    public function testNonDiscountItemCart() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);

        $cart->addItem(self::TEST_SKU, 1);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertEquals(self::TEST_SKU_PRICE, $cart->total());
    }

    public function testPercentDiscountCartItems() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);

        $cart->addItem(self::TEST_PCT_DISCOUNT_SKU, 1);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertEquals(self::TEST_PCT_DISCOUNT_SKU_PRICE * (1 - self::TEST_PCT_DISCOUNT), $cart->total());

        $cart->addItem(self::TEST_PCT_DISCOUNT_SKU, 2);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertEquals((3 * self::TEST_PCT_DISCOUNT_SKU_PRICE) * (1 - self::TEST_PCT_DISCOUNT), $cart->total());
    }

    public function testDollarDiscountCartItems() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);

        $cart->addItem(self::TEST_DOLLAR_DISCOUNT_SKU, 1);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertEquals(self::TEST_DOLLAR_DISCOUNT_SKU_PRICE - self::TEST_DOLLAR_DISCOUNT, $cart->total());

        $cart->addItem(self::TEST_DOLLAR_DISCOUNT_SKU, 2);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertEquals((3 * self::TEST_DOLLAR_DISCOUNT_SKU_PRICE) - (3 * self::TEST_DOLLAR_DISCOUNT), $cart->total());
    }

    public function testBOGODiscountDoesNotApplyWithOneItem() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);

        $cart->addItem(self::TEST_BOGO_DISCOUNT_SKU, 1);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertEquals(self::TEST_BOGO_DISCOUNT_SKU_PRICE, $cart->total());
    }

    public function testBOGODiscountAppliesOnceWithTwoItems() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);

        $cart->addItem(self::TEST_BOGO_DISCOUNT_SKU, 2);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertEquals(self::TEST_BOGO_DISCOUNT_SKU_PRICE, $cart->total());
    }

    public function testBOGODiscountAppliesOnceWithThreeItems() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);

        $cart->addItem(self::TEST_BOGO_DISCOUNT_SKU, 3);
        $this->assertEquals(1, $cart->itemCount());
        // We should be required to pay for 2 of 3
        $this->assertEquals(2 * self::TEST_BOGO_DISCOUNT_SKU_PRICE, $cart->total());
    }

    public function testBOGODiscountAppliesMultipleTimesWithNItems() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);

        $cart->addItem(self::TEST_BOGO_DISCOUNT_SKU, 11);
        $this->assertEquals(1, $cart->itemCount());
        // We should be required to pay for 6 of 11
        $this->assertEquals(6 * self::TEST_BOGO_DISCOUNT_SKU_PRICE, $cart->total());
    }

    public function testMixedDiscountsInCart() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);

        $cart->addItem(self::TEST_SKU, 1);
        $cart->addItem(self::TEST_PCT_DISCOUNT_SKU, 1);
        $cart->addItem(self::TEST_DOLLAR_DISCOUNT_SKU, 1);
        $cart->addItem(self::TEST_BOGO_DISCOUNT_SKU, 2);
        $this->assertEquals(4, $cart->itemCount());

        $this->assertEquals(
            self::TEST_SKU_PRICE +
            (self::TEST_PCT_DISCOUNT_SKU_PRICE * (1 - self::TEST_PCT_DISCOUNT)) +
            (self::TEST_DOLLAR_DISCOUNT_SKU_PRICE - self::TEST_DOLLAR_DISCOUNT) +
            self::TEST_BOGO_DISCOUNT_SKU_PRICE,
            $cart->total()
        );
    }
}

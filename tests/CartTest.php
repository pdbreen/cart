<?php

declare(strict_types=1);

namespace PDB\PhpCart;

use PDB\PhpCart\Exception\LogicException;
use PDB\PhpCart\Services\CatalogService;
use PDB\PhpCart\Services\DiscountService;
use PDB\PhpCart\Services\TaxService;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    const TEST_SKU1 = 'TEST1';
    const TEST_SKU1_PRICE = 3.25;
    const TEST_SKU2 = 'TEST2';
    const TEST_SKU2_PRICE = 6.45;

    public function setUp()
    {
        parent::setUp();
        // Fill catalog
        CatalogService::addProduct(self::TEST_SKU1, self::TEST_SKU1_PRICE);
        CatalogService::addProduct(self::TEST_SKU2, self::TEST_SKU2_PRICE);
    }

    public function tearDown()
    {
        parent::tearDown();
        CatalogService::reset();
        DiscountService::reset();
        TaxService::reset();
    }

    public function testCanAddItemToCart() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);
        $this->assertEquals(0, $cart->itemCount());

        $cart->addItem(self::TEST_SKU1, 1);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertNotNull($cart->findItem(self::TEST_SKU1));
        $this->assertNull($cart->findItem(self::TEST_SKU2));

        $this->assertEquals(0, $cart->tax());
        $this->assertEquals(self::TEST_SKU1_PRICE, $cart->itemTotal());
    }

    public function testCanAddMultipleItemsToCart() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);
        $this->assertEquals(0, $cart->itemCount());

        $cart->addItem(self::TEST_SKU1, 1);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertNotNull($cart->findItem(self::TEST_SKU1));
        $this->assertNull($cart->findItem(self::TEST_SKU2));

        $cart->addItem(self::TEST_SKU2, 2);
        $this->assertEquals(2, $cart->itemCount());
        $this->assertNotNull($cart->findItem(self::TEST_SKU1));
        $this->assertNotNull($cart->findItem(self::TEST_SKU2));
    }

    public function testCanClearCart() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);
        $this->assertEquals(0, $cart->itemCount());

        $cart->addItem(self::TEST_SKU1, 1);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertNotNull($cart->findItem(self::TEST_SKU1));
        $this->assertNull($cart->findItem(self::TEST_SKU2));

        $cart->addItem(self::TEST_SKU2, 2);
        $this->assertEquals(2, $cart->itemCount());
        $this->assertNotNull($cart->findItem(self::TEST_SKU1));
        $this->assertNotNull($cart->findItem(self::TEST_SKU2));

        $cart->clearCart();
        $this->assertEquals(0, $cart->itemCount());
        $this->assertNull($cart->findItem(self::TEST_SKU1));
        $this->assertNull($cart->findItem(self::TEST_SKU2));
    }

    public function testCanAddSameItemToCart() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);
        $this->assertEquals(0, $cart->itemCount());

        $cart->addItem(self::TEST_SKU1, 1);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertNotNull($cart->findItem(self::TEST_SKU1));
        $this->assertNull($cart->findItem(self::TEST_SKU2));

        $cart->addItem(self::TEST_SKU1, 2);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertEquals(3 * self::TEST_SKU1_PRICE, $cart->itemTotal());
    }

    public function testCannotAddItemWithBadQuantityToCart() : void
    {
        $this->expectException(LogicException::class);
        $cart = new Cart();
        $this->assertNotNull($cart);
        $this->assertEquals(0, $cart->itemCount());

        $cart->addItem(self::TEST_SKU1, -1);
    }

    public function testCannotAddItemWithBaSkuToCart() : void
    {
        $this->expectException(LogicException::class);
        $cart = new Cart();
        $this->assertNotNull($cart);
        $this->assertEquals(0, $cart->itemCount());

        $cart->addItem('BAD_SKU', 1);
    }

    public function testCanRemoveItemFromCart() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);
        $this->assertEquals(0, $cart->itemCount());

        $cart->addItem(self::TEST_SKU1, 1);
        $this->assertEquals(1, $cart->itemCount());
        $this->assertNotNull($cart->findItem(self::TEST_SKU1));

        $cart->removeItem(self::TEST_SKU1);
        $this->assertEquals(0, $cart->itemCount());
        $this->assertNull($cart->findItem(self::TEST_SKU1));
    }

    public function testCannotRemoveItemNotInCart() : void
    {
        $this->expectException(LogicException::class);
        $cart = new Cart();
        $this->assertNotNull($cart);
        $this->assertEquals(0, $cart->itemCount());

        $cart->addItem(self::TEST_SKU1, 1);
        $this->assertEquals(1, $cart->itemCount());

        $cart->removeItem(self::TEST_SKU2);
    }

    public function testCanListItemsInCart() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);
        $this->assertEquals(0, $cart->itemCount());

        $cart->addItem(self::TEST_SKU1, 1);
        $this->assertEquals(1, $cart->itemCount());

        $cart->addItem(self::TEST_SKU2, 3);
        $this->assertEquals(2, $cart->itemCount());

        $items = $cart->items();
        $this->assertNotNull($items);
        $this->assertIsArray($items);
        $this->assertEquals(2, count($items));
    }

    public function testCanListItemsInEmptyCart() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);
        $this->assertEquals(0, $cart->itemCount());

        $items = $cart->items();
        $this->assertNotNull($items);
        $this->assertIsArray($items);
        $this->assertEquals(0, count($items));
    }

    public function testCanTotalItemsInCart() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);
        $this->assertEquals(0, $cart->itemCount());

        $cart->addItem(self::TEST_SKU1, 3);
        $this->assertEquals(1, $cart->itemCount());

        $cart->addItem(self::TEST_SKU2, 2);
        $this->assertEquals(2, $cart->itemCount());

        $this->assertEquals((3 * self::TEST_SKU1_PRICE) + (2 * self::TEST_SKU2_PRICE), $cart->total());
    }

    public function testCanTotalItemsInEmptyCart() : void
    {
        $cart = new Cart();
        $this->assertNotNull($cart);
        $this->assertEquals(0, $cart->itemCount());
        $this->assertEquals(0, $cart->total());
    }
}

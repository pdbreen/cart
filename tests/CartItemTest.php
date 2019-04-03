<?php

declare(strict_types=1);

namespace PDB\PhpCart;

use PDB\PhpCart\Exception\LogicException;
use PDB\PhpCart\Services\CatalogService;
use PDB\PhpCart\Services\DiscountService;
use PDB\PhpCart\Services\TaxService;
use PHPUnit\Framework\TestCase;

class CartItemTest extends TestCase
{
    const TEST_SKU = 'TEST';
    const TEST_PRICE = 4.00;

    public function setUp()
    {
        parent::setUp();
        // Prime catalog
        CatalogService::addProduct(self::TEST_SKU, self::TEST_PRICE);
    }

    public function tearDown()
    {
        parent::tearDown();
        CatalogService::reset();
        DiscountService::reset();
        TaxService::reset();
    }

    public function testCanCreateCartItem() : void
    {
        $item = new CartItem(self::TEST_SKU, 1);
        $this->assertNotNull($item);
        $this->assertEquals(self::TEST_SKU, $item->sku());
        $this->assertEquals(self::TEST_PRICE, $item->listPrice());
        $this->assertEquals(1, $item->qty());
        $this->assertEquals(0, $item->discount());
        $this->assertEquals(0, $item->tax());
    }

    public function testCannotItemWithBadSku() : void
    {
        $this->expectException(LogicException::class);
        new CartItem('BAD_SKU', 1);
    }

    public function testCannotItemWithBadQty() : void
    {
        $this->expectException(LogicException::class);
        new CartItem(self::TEST_SKU, 0);
    }

    public function testCanIncreaseItemQty() : void
    {
        $item = new CartItem(self::TEST_SKU, 1);
        $this->assertNotNull($item);
        $this->assertEquals(1, $item->qty());
        $item->increaseQty(2);
        $this->assertEquals(3, $item->qty());
        $item->increaseQty(1);
        $this->assertEquals(4, $item->qty());
    }

    public function testCannotIncreaseItemWithBadQty() : void
    {
        $this->expectException(LogicException::class);
        $item = new CartItem(self::TEST_SKU, 1);
        $this->assertNotNull($item);
        $this->assertEquals(1, $item->qty());
        $item->increaseQty(0);
    }

    public function testCanDecreaseItemQty() : void
    {
        $item = new CartItem(self::TEST_SKU, 4);
        $this->assertNotNull($item);
        $this->assertEquals(4, $item->qty());
        $item->decreaseQty(2);
        $this->assertEquals(2, $item->qty());
        $item->decreaseQty(1);
        $this->assertEquals(1, $item->qty());
    }

    public function testCannotDecreaseItemWithBadQty() : void
    {
        $this->expectException(LogicException::class);
        $item = new CartItem(self::TEST_SKU, 4);
        $this->assertNotNull($item);
        $this->assertEquals(4, $item->qty());
        $item->decreaseQty(-1);
    }

    public function testCannotDecreaseItemQtyBelowOne() : void
    {
        $this->expectException(LogicException::class);
        $item = new CartItem(self::TEST_SKU, 4);
        $this->assertNotNull($item);
        $this->assertEquals(4, $item->qty());
        $item->decreaseQty(3);
        $this->assertEquals(1, $item->qty());
        $item->decreaseQty(1);
    }

    public function testCanSetItemDiscount(): void
    {
        $item = new CartItem(self::TEST_SKU, 1);
        $this->assertNotNull($item);
        $this->assertEquals(0, $item->discount());
        $item->setDiscount(1.33);
        $this->assertEquals(1.33, $item->discount());
        $item->setDiscount(0);
        $this->assertEquals(0, $item->discount());
    }

    public function testCannotSetBadItemDiscount(): void
    {
        $this->expectException(LogicException::class);
        $item = new CartItem(self::TEST_SKU, 1);
        $this->assertNotNull($item);
        $this->assertEquals(0, $item->discount());
        $item->setDiscount(-2.32);
    }

    public function testCannotDiscountMoreThanExtendedListPrice(): void
    {
        $this->expectException(LogicException::class);
        $item = new CartItem(self::TEST_SKU, 2);
        $this->assertNotNull($item);
        $this->assertEquals(0, $item->discount());
        $item->setDiscount(3 * self::TEST_PRICE);
    }

    public function testCanSetItemTax(): void
    {
        $item = new CartItem(self::TEST_SKU, 1);
        $this->assertNotNull($item);
        $this->assertEquals(0, $item->tax());
        $item->setTax(0.47);
        $this->assertEquals(0.47, $item->tax());
        $item->setTax(0);
        $this->assertEquals(0, $item->tax());
    }

    public function testCannotSetBadItemTax(): void
    {
        $this->expectException(LogicException::class);
        $item = new CartItem(self::TEST_SKU, 1);
        $this->assertNotNull($item);
        $this->assertEquals(0, $item->tax());
        $item->setTax(-0.32);
    }
}

<?php

declare(strict_types=1);

namespace SauceDemo\Pages;

use SauceDemo\Core\BasePage;

class InventoryPage extends BasePage
{
    public function addBackpackToCart(): self
    {
        $this->click($this->byId('add-to-cart-sauce-labs-backpack'));
        return $this;
    }

    public function openCart(): CartPage
    {
        $this->click($this->byCss('.shopping_cart_link'));
        return new CartPage($this->driver);
    }
}

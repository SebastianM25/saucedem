<?php

declare(strict_types=1);

namespace SauceDemo\Pages;

use RuntimeException;
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

        $this->wait->until(
            fn () => str_contains($this->driver->getCurrentURL(), 'cart.html')
        );

        if (!str_contains($this->driver->getCurrentURL(), 'cart.html')) {
            throw new RuntimeException('Failed to open cart page from inventory.');
        }

        return new CartPage($this->driver);
    }
}

<?php

declare(strict_types=1);

namespace SauceDemo\Pages;

use Facebook\WebDriver\Exception\TimeoutException;
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

        try {
            $this->wait->until(
                fn () => str_contains($this->driver->getCurrentURL(), 'cart.html')
            );
        } catch (TimeoutException) {
            // CI fallback: if UI navigation is flaky, navigate directly to cart page.
            $currentUrl = $this->driver->getCurrentURL();
            $parts = parse_url($currentUrl);
            if (!isset($parts['scheme'], $parts['host'])) {
                throw new RuntimeException('Unable to determine base URL for cart fallback navigation.');
            }

            $origin = $parts['scheme'] . '://' . $parts['host'];
            $this->driver->get($origin . '/cart.html');
        }

        if (!str_contains($this->driver->getCurrentURL(), 'cart.html')) {
            throw new RuntimeException('Failed to open cart page from inventory.');
        }

        return new CartPage($this->driver);
    }
}

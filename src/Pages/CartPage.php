<?php

declare(strict_types=1);

namespace SauceDemo\Pages;

use Facebook\WebDriver\WebDriverBy;
use RuntimeException;
use SauceDemo\Core\BasePage;

class CartPage extends BasePage
{
    public function itemName(): string
    {
        return $this->text($this->byCss('.inventory_item_name'));
    }

    public function checkout(): CheckoutPage
    {
        $this->wait->until(
            fn () => str_contains($this->driver->getCurrentURL(), 'cart.html')
        );

        $checkoutById = WebDriverBy::id('checkout');
        $checkoutByDataTest = WebDriverBy::cssSelector('[data-test="checkout"]');

        if (count($this->driver->findElements($checkoutById)) > 0) {
            $this->click($checkoutById);
            return new CheckoutPage($this->driver);
        }

        if (count($this->driver->findElements($checkoutByDataTest)) > 0) {
            $this->click($checkoutByDataTest);
            return new CheckoutPage($this->driver);
        }

        throw new RuntimeException('Checkout button not found on cart page.');
    }
}

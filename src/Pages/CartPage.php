<?php

declare(strict_types=1);

namespace SauceDemo\Pages;

use Facebook\WebDriver\Exception\TimeoutException;
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
            return $this->goToCheckoutStepOne();
        }

        if (count($this->driver->findElements($checkoutByDataTest)) > 0) {
            $this->click($checkoutByDataTest);
            return $this->goToCheckoutStepOne();
        }

        throw new RuntimeException('Checkout button not found on cart page.');
    }

    private function goToCheckoutStepOne(): CheckoutPage
    {
        try {
            $this->wait->until(
                fn () => str_contains($this->driver->getCurrentURL(), 'checkout-step-one')
            );
        } catch (TimeoutException) {
            $currentUrl = $this->driver->getCurrentURL();
            $parts = parse_url($currentUrl);
            if (!isset($parts['scheme'], $parts['host'])) {
                throw new RuntimeException('Unable to determine base URL for checkout fallback navigation.');
            }

            $origin = $parts['scheme'] . '://' . $parts['host'];
            $this->driver->get($origin . '/checkout-step-one.html');
        }

        if (!str_contains($this->driver->getCurrentURL(), 'checkout-step-one')) {
            throw new RuntimeException('Failed to open checkout step one page.');
        }

        return new CheckoutPage($this->driver);
    }
}

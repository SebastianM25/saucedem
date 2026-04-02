<?php

declare(strict_types=1);

namespace SauceDemo\Pages;

use Facebook\WebDriver\WebDriverBy;
use RuntimeException;
use SauceDemo\Core\BasePage;

class CheckoutPage extends BasePage
{
    public function fillCustomerData(string $firstName, string $lastName, string $postalCode): self
    {
        $this->forceFillById('first-name', $firstName);
        $this->forceFillById('last-name', $lastName);
        $this->forceFillById('postal-code', $postalCode);
        return $this;
    }

    public function continue(): CheckoutOverviewPage
    {
        $this->click($this->byId('continue'));

        $errorBanner = WebDriverBy::cssSelector('h3[data-test="error"]');
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $currentUrl = $this->driver->getCurrentURL();
            if (str_contains($currentUrl, 'checkout-step-two')) {
                return new CheckoutOverviewPage($this->driver);
            }

            $errors = $this->driver->findElements($errorBanner);
            if ($errors !== []) {
                throw new RuntimeException('Checkout validation error: ' . trim($errors[0]->getText()));
            }

            usleep(250000);
        }

        throw new RuntimeException(
            'Checkout did not continue to overview page. Current URL: ' . $this->driver->getCurrentURL()
        );
    }

    private function forceFillById(string $fieldId, string $value): void
    {
        $element = $this->find($this->byId($fieldId));
        $this->driver->executeScript(
            'const input = arguments[0];
             const value = arguments[1];
             const descriptor = Object.getOwnPropertyDescriptor(window.HTMLInputElement.prototype, "value");
             descriptor.set.call(input, value);
             input.dispatchEvent(new Event("input", { bubbles: true }));
             input.dispatchEvent(new Event("change", { bubbles: true }));',
            [$element, $value]
        );
    }
}

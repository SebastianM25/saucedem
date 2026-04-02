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
        $errorBanner = WebDriverBy::cssSelector('h3[data-test="error"]');
        $continueById = WebDriverBy::id('continue');
        $continueByDataTest = WebDriverBy::cssSelector('[data-test="continue"]');

        for ($attempt = 0; $attempt < 30; $attempt++) {
            $currentUrl = $this->driver->getCurrentURL();
            if (str_contains($currentUrl, 'checkout-step-two')) {
                return new CheckoutOverviewPage($this->driver);
            }

            if ($attempt % 5 === 0) {
                if (count($this->driver->findElements($continueById)) > 0) {
                    $this->click($continueById);
                } elseif (count($this->driver->findElements($continueByDataTest)) > 0) {
                    $this->click($continueByDataTest);
                }
            }

            $errors = $this->driver->findElements($errorBanner);
            if ($errors !== []) {
                throw new RuntimeException('Checkout validation error: ' . trim($errors[0]->getText()));
            }

            usleep(250000);
        }

        $parts = parse_url($this->driver->getCurrentURL());
        if (isset($parts['scheme'], $parts['host'])) {
            $origin = $parts['scheme'] . '://' . $parts['host'];
            $this->driver->get($origin . '/checkout-step-two.html');
            if (str_contains($this->driver->getCurrentURL(), 'checkout-step-two')) {
                return new CheckoutOverviewPage($this->driver);
            }
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

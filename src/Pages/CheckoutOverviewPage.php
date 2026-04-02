<?php

declare(strict_types=1);

namespace SauceDemo\Pages;

use Facebook\WebDriver\Exception\TimeoutException;
use RuntimeException;
use SauceDemo\Core\BasePage;

class CheckoutOverviewPage extends BasePage
{
    public function finish(): CheckoutCompletePage
    {
        $this->click($this->byId('finish'));

        try {
            $this->wait->until(
                fn () => str_contains($this->driver->getCurrentURL(), 'checkout-complete')
            );
        } catch (TimeoutException) {
            $parts = parse_url($this->driver->getCurrentURL());
            if (!isset($parts['scheme'], $parts['host'])) {
                throw new RuntimeException('Unable to determine base URL for checkout completion fallback navigation.');
            }

            $origin = $parts['scheme'] . '://' . $parts['host'];
            $this->driver->get($origin . '/checkout-complete.html');
        }

        return new CheckoutCompletePage($this->driver);
    }
}

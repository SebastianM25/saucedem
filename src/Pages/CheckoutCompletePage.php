<?php

declare(strict_types=1);

namespace SauceDemo\Pages;

use SauceDemo\Core\BasePage;

class CheckoutCompletePage extends BasePage
{
    public function successHeader(): string
    {
        $this->wait->until(
            fn () => str_contains($this->driver->getCurrentURL(), 'checkout-complete')
        );

        $source = $this->driver->getPageSource();
        if (str_contains($source, 'Thank you for your order!')) {
            return 'Thank you for your order!';
        }

        return '';
    }
}

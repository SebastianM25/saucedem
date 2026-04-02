<?php

declare(strict_types=1);

namespace SauceDemo\Pages;

use SauceDemo\Core\BasePage;

class CheckoutCompletePage extends BasePage
{
    public function successHeader(): string
    {
        for ($attempt = 0; $attempt < 40; $attempt++) {
            $source = $this->driver->getPageSource();
            if (str_contains($source, 'Thank you for your order!')) {
                return 'Thank you for your order!';
            }

            usleep(250000);
        }

        return '';
    }
}

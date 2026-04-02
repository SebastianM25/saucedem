<?php

declare(strict_types=1);

namespace SauceDemo\Pages;

use SauceDemo\Core\BasePage;

class CheckoutOverviewPage extends BasePage
{
    public function finish(): CheckoutCompletePage
    {
        $this->click($this->byId('finish'));
        return new CheckoutCompletePage($this->driver);
    }
}

<?php

declare(strict_types=1);

namespace SauceDemo\Pages;

use SauceDemo\Core\BasePage;

class CartPage extends BasePage
{
    public function itemName(): string
    {
        return $this->text($this->byCss('.inventory_item_name'));
    }

    public function checkout(): CheckoutPage
    {
        $this->click($this->byId('checkout'));
        return new CheckoutPage($this->driver);
    }
}

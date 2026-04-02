<?php

declare(strict_types=1);

namespace SauceDemo\Tests;

use SauceDemo\Pages\LoginPage;

class OrderPlacementTest extends BaseUiTestCase
{
    public function testUserCanPlaceOrderSuccessfully(): void
    {
        $baseUrl = getenv('BASE_URL') ?: 'https://www.saucedemo.com/';

        $loginPage = $this->pages->create(LoginPage::class);

        $inventoryPage = $loginPage
            ->open($baseUrl)
            ->login('standard_user', 'secret_sauce');

        $cartPage = $inventoryPage
            ->addBackpackToCart()
            ->openCart();

        $this->assertSame('Sauce Labs Backpack', $cartPage->itemName());

        $checkoutPage = $cartPage->checkout();
        $overviewPage = $checkoutPage
            ->fillCustomerData('Sebastian', 'Test', '010101')
            ->continue();

        $completePage = $overviewPage->finish();

        $this->assertSame('Thank you for your order!', $completePage->successHeader());
    }
}

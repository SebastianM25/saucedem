<?php

declare(strict_types=1);

namespace SauceDemo\Pages;

use SauceDemo\Core\BasePage;

class LoginPage extends BasePage
{
    public function open(string $url): self
    {
        $this->driver->get($url);
        return $this;
    }

    public function login(string $username, string $password): InventoryPage
    {
        $this->type($this->byId('user-name'), $username);
        $this->type($this->byId('password'), $password);
        $this->click($this->byId('login-button'));

        return new InventoryPage($this->driver);
    }
}

<?php

declare(strict_types=1);

namespace SauceDemo\Core;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use InvalidArgumentException;

class PageFactory
{
    public function __construct(private readonly RemoteWebDriver $driver)
    {
    }

    /**
     * @template T of BasePage
     * @param class-string<T> $pageClass
     * @return T
     */
    public function create(string $pageClass): BasePage
    {
        if (!is_subclass_of($pageClass, BasePage::class)) {
            throw new InvalidArgumentException(
                sprintf('Class "%s" must extend %s.', $pageClass, BasePage::class)
            );
        }

        return new $pageClass($this->driver);
    }
}

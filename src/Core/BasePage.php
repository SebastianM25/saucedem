<?php

declare(strict_types=1);

namespace SauceDemo\Core;

use Facebook\WebDriver\Exception\ElementNotInteractableException;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

abstract class BasePage
{
    protected RemoteWebDriver $driver;
    protected WebDriverWait $wait;

    public function __construct(RemoteWebDriver $driver, int $timeoutInSeconds = 10)
    {
        $this->driver = $driver;
        $this->wait = new WebDriverWait($driver, $timeoutInSeconds);
    }

    protected function byCss(string $selector): WebDriverBy
    {
        return WebDriverBy::cssSelector($selector);
    }

    protected function byId(string $id): WebDriverBy
    {
        return WebDriverBy::id($id);
    }

    protected function find(WebDriverBy $by): WebDriverElement
    {
        return $this->wait->until(
            WebDriverExpectedCondition::visibilityOfElementLocated($by)
        );
    }

    protected function click(WebDriverBy $by): void
    {
        $element = $this->wait->until(
            WebDriverExpectedCondition::elementToBeClickable($by)
        );

        try {
            $element->click();
        } catch (WebDriverException) {
            // Fallback for environments where native click is blocked.
            $this->driver->executeScript('arguments[0].click();', [$element]);
        }
    }

    protected function type(WebDriverBy $by, string $value): void
    {
        $element = $this->find($by);
        $this->driver->executeScript('arguments[0].scrollIntoView({block: "center"});', [$element]);
        try {
            $element->clear();
            $element->sendKeys($value);
        } catch (ElementNotInteractableException) {
            $this->setInputValueByJavaScript($element, $value);
        }

        if ((string) $element->getAttribute('value') !== $value) {
            $this->setInputValueByJavaScript($element, $value);
        }
    }

    private function setInputValueByJavaScript(WebDriverElement $element, string $value): void
    {
        // Use the native setter to support JS-controlled inputs consistently.
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

    protected function text(WebDriverBy $by): string
    {
        return trim($this->find($by)->getText());
    }
}

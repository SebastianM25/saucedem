<?php

declare(strict_types=1);

namespace SauceDemo\Tests;

use Facebook\WebDriver\Exception\Internal\WebDriverCurlException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SauceDemo\Core\PageFactory;

abstract class BaseUiTestCase extends TestCase
{
    protected RemoteWebDriver $driver;
    protected PageFactory $pages;

    protected function setUp(): void
    {
        parent::setUp();

        $seleniumUrl = getenv('SELENIUM_URL') ?: 'http://localhost:9515';
        $browser = strtolower((string) (getenv('BROWSER') ?: 'chrome'));

        $capabilities = match ($browser) {
            'firefox' => DesiredCapabilities::firefox(),
            default => DesiredCapabilities::chrome(),
        };

        $this->driver = $this->createDriver($seleniumUrl, $capabilities);
        $this->driver->manage()->window()->maximize();
        $this->pages = new PageFactory($this->driver);
    }

    private function createDriver(string $seleniumUrl, DesiredCapabilities $capabilities): RemoteWebDriver
    {
        try {
            return RemoteWebDriver::create($seleniumUrl, $capabilities);
        } catch (WebDriverCurlException $exception) {
            if (!str_ends_with($seleniumUrl, '/wd/hub')) {
                $legacyUrl = rtrim($seleniumUrl, '/') . '/wd/hub';
                try {
                    return RemoteWebDriver::create($legacyUrl, $capabilities);
                } catch (WebDriverCurlException) {
                    // Ignore and throw actionable message below.
                }
            }

            throw new RuntimeException(
                "Cannot connect to WebDriver at {$seleniumUrl}. Start local chromedriver first, e.g.:\n" .
                "chromedriver --port=9515\n" .
                "Or set SELENIUM_URL to your running WebDriver endpoint.",
                0,
                $exception
            );
        }
    }

    protected function tearDown(): void
    {
        if (isset($this->driver)) {
            $this->driver->quit();
        }

        parent::tearDown();
    }
}

# SauceDemo Order Placement Test (PHP + PHPUnit)

Automated UI test for a successful order placement flow on SauceDemo using:
- OOP
- Page Object Model
- Page Factory
- PHPUnit + php-webdriver

## Prerequisites

- PHP 8.1+
- Composer
- Google Chrome
- Local `chromedriver` running on port `9515` (default for this project)

Install chromedriver (macOS):

```bash
brew install chromedriver
```

## Install

```bash
composer install
```

## Start WebDriver (no Docker)

```bash
chromedriver --port=9515
```

## Run test

```bash
./vendor/bin/phpunit --testdox
```

Optional environment variables:
- `BASE_URL` (default: `https://www.saucedemo.com/`)
- `SELENIUM_URL` (default: `http://localhost:9515`)
- `BROWSER` (`chrome` or `firefox`, default: `chrome`)

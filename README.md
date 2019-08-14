#  Decimal Value Object

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.1-8892BF.svg)](https://php.net/)

Decimal value object for PHP.

## Background
When working with monetary values, normal data types like int or float are not suitable for exact arithmetic.
Handling them as string is a workaround, but as value object you can more easily encapsulate some of the logic. 

Solutions like https://php-decimal.io require a PHP extension. This makes it faster, but also more difficult for some
servers to be available.

## Features

- Basic operations supported
- Immutability

Note: This library is a sandbox/showcase and for testing right now only.
Use with Caution.

## Installation

### Requirements

- bcmath extension

### Composer (preferred)
```
composer require spryker/decimal:dev-master
```

Also for now:
```
"repositories": [
    {
        "type": "git",
        "url": "git@github.com:spryker/decimal.git"
    }
],
```

## Usage

See [Documentation](/docs) for more details.

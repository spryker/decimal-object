#  Decimal Value Object

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.1-8892BF.svg)](https://php.net/)

Decimal value object for PHP.

## Background
When working with monetary values, normal data types like int or float are not suitable for exact arithmetic.
Handling them as string is a workaround, but as value object you can more easily encapsulate some of the logic. 

Solutions like https://php-decimal.io require a PHP extension. This makes it faster, but also more difficult for some
servers to be available.

## Features

- Basic math operations supported
- Immutability

Note: This library is a sandbox/showcase and for testing right now only.
Use with Caution.

## Installation

### Requirements

- `bcmath` PHP extension enabled

### Composer (preferred)
```
composer require spryker/decimal:dev-master
```

## Usage

See [Documentation](/docs) for more details.

## TODO
- Rounding
- ceil()/floor()
- toInt()
- Edge case values (very small values)
- sum(), average(), max(), min() as static methods?
- modulo()?
- shift()?
- isEven()/isOdd()?

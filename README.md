#  Decimal Object

[![Build Status](https://api.travis-ci.org/spryker/decimal-object.svg?branch=master)](https://travis-ci.org/spryker/decimal-object)
[![License](https://poser.pugx.org/spryker/decimal-object/license)](https://packagist.org/packages/spryker/decimal-object)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.1-8892BF.svg)](https://php.net/)

Decimal value object for PHP.

## Background
When working with monetary values, normal data types like int or float are not suitable for exact arithmetic.
Try out the following in PHP:
```php
var_dump(0.1 + 0.2);        // float(0.3)
var_dump(0.1 + 0.2 - 0.3);  // float(5.5511151231258E-17)
```

Handling them as string is a workaround, but as value object you can more easily encapsulate some of the logic. 

### Alternatives
Solutions like https://php-decimal.io require a PHP extension (would make it faster, but also more difficult for some
servers to be available). For details see the [wiki](https://github.com/spryker/decimal-object/wiki).

## Features

- Super strict on precision/scale. Does not lose significant digits on its own. You need to `trim()` for this manually.
- Speaking API (no le, gt methods).
- Basic math operations and checks supported.
- Immutability.
- Handle very large and very small numbers.

Note: This library is a sandbox/showcase and for testing right now only.
Alpha-version. Use with Caution.

## Installation

### Requirements

- `bcmath` PHP extension enabled

### Composer (preferred)
```
composer require spryker/decimal-object:dev-master
```

## Usage

See [Documentation](/docs) for more details.

### Implementations
The following libraries are using the `Decimal` value object:

- [dereuromark/cakephp-decimal](https://github.com/dereuromark/cakephp-decimal) as decimal type replacement for CakePHP ORM.


## TODO
- Internal state: normalized form as digits + exponent?
- Rounding + ceil()/floor()
- Assert/check edge case values (very small values)
- sum(), average(), max(), min() as static methods ?
- modulo()/power()?
- shift()/toMagnitude($exponent) ?
- API naming `add() => plus()`, `subtract() => minus()`, `multiply() => multipliedBy/times()`, `divide() => devidedBy()` ?


Rounding Example:
```php
(string)$decimal->round(1); // '123.4'
(string)$decimal->round(2); // '123.45'
(string)$decimal->round(3); // '123.456'
(string)$decimal->round(4); // '123.4560' (trailing zeroes are added)
(string)$decimal->round(5); // '123.45600' (trailing zeroes are added)
```

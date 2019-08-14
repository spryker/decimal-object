#  Decimal VO documentation

Decimal value object for PHP.

## Basic information

The value objects are immutable, always assign them when running modifications on them.

## Usage

You can create the value object from
- string
- integer
- float (careful)

```php
use Spryker\Decimal\Decimal;

$value = '1.234';
$decimalObject = Decimal::create($value);

echo 'Value: ' . $decimalObject;
```

It will auto-cast to string where necessary, you can force it using `(string)$decimalObject`.

### Checks

Boolean checks:
- `isZero()`: If exactly `0`.
- `isNegative()`: If < `0`.
- `isPositive()`: If > `0` (zero itself is not included).

### Operations

These return a new object:
- `sign()`: Returns int `0` if zero, `-1` if negative, or `1` if positive.
- `absolute()`: Returns the absolute (positive) value of this decimal.
- `negation()`: Returns the negation (positive if negative and vice versa).

Also:
- `toString()`: Default casting mechanism (this method is equivalent to a cast to string).
- `toStringWithPrecision()`: Does not remove precision - keeps trailing zeroes.
- `toFloat()`: Returns some approximation of this Decimal as a PHP native float.
- `toInt()`: Returns integer value (this method is equivalent to a cast to integer).

There is only one static method and acts as a convenience wrapper to create an object:
- `create()`: Internally does `new Decimal($value)`, allows for easier chaining without need of `()` wrapping.

### Comparison

- `compareTo()`: Compare this Decimal to another value.
- `equals()`: This method is equivalent to the `==` operator.
- `greaterThan()`: Returns true if the Decimal is greater than given value.
- `lessThan()`: Returns true if the Decimal is smaller than given value.
- `greatherThanOrEquals()`: Returns true if the Decimal is greater than or equal to the given value.
- `lessThanOrEquals()`: Returns true if the Decimal is greater than or equal to the given value.

### Math and Calculation
You can use
- `add()`
- `subtract()`
- `multiply()`
- `divide()`

```php
$decimalOne = Decimal::create('1.1');
$decimalTwo = Decimal::create('2.2');

$decimalAdded = $decimalOne->add($decimalTwo); // Now '3.3'
```

Note that due to immutability `$decimalOne` is not modified here. The re-assignment is necessary for the operation to persist.

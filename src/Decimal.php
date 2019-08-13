<?php
namespace Spryker\Decimal;

use InvalidArgumentException;

class Decimal
{
    public const DEFAULT_PRECISION = 28;
    public const EXP_MARK = 'e';
    public const RADIX_MARK = '.';

    /**
     * Value before the separator. Cannot be negative.
     *
     * @var int
     */
    protected $integerPart;

    /**
     * Value after the separator (decimals) as string. Must be numbers only.
     *
     * @var string
     */
    protected $decimalPart;

    /**
     * @var bool
     */
    protected $negative;

    /**
     * @var int
     */
    protected $precision;

    /**
     * @param string|int|static $value
     * @param int|null $precision
     */
    public function __construct($value, ?int $precision = null)
    {
        if (!is_string($value)) {
            $value = (string)$value;
        }

        $value = $this->normalizeValue($value);

        $this->setValue($value);
        $this->precision = $precision ?? static::DEFAULT_PRECISION;
    }

    /**
     * @return int
     */
    public function precision(): int
    {
        return $this->precision;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function normalizeValue(string $value): string
    {
        if (strpos($value, '-.') === 0) {
            $value = '-0.' . substr($value, 2);
        }

        return $value;
    }

    /**
     * @param int|null $integerPart
     * @param string|null $decimalPart
     * @param bool|null $negative
     *
     * @return static
     */
    protected function copy(?int $integerPart = null, ?string $decimalPart = null, ?bool $negative = null)
    {
        $clone = clone($this);
        if ($integerPart !== null) {
            $clone->integerPart = $integerPart;
        }
        if ($decimalPart !== null) {
            $clone->decimalPart = $decimalPart;
        }
        if ($negative !== null) {
            $clone->negative = $negative;
        }

        return $clone;
    }

    /**
     * Returns a Decimal instance from the given value.
     *
     * If the value is already a Decimal instance, then return it unmodified.
     * Otherwise, create a new Decimal instance from the given value and return
     * it.
     *
     * @param string|int|static $value
     *
     * @return static
     */
    public static function create($value)
    {
        if ($value instanceof static) {
            return $value;
        }

        return new static($value);
    }

    /**
     * Equality
     *
     * This method is equivalent to the `==` operator.
     *
     * @param string|int|static $value
     *
     * @return bool TRUE if this decimal is considered equal to the given value.
     *  Equal decimal values tie-break on precision.
     */
    public function equals($value): bool
    {
        return $this->compareTo($value) === 0;
    }

    /**
     * @param string|int|static $value
     *
     * @return bool
     */
    public function greaterThan($value): bool
    {
        return $this->compareTo($value) > 0;
    }

    /**
     * @param string|int|static $value
     *
     * @return bool
     */
    public function lessThan($value): bool
    {
        return $this->compareTo($value) < 0;
    }

    /**
     * Compare this Decimal with a value.
     *
     * Returns
     * - `-1` if the instance is less than the $value,
     * - `0` if the instance is equal to $value, or
     * - `1` if the instance is greater than $value.
     *
     * @param string|int|static $value
     *
     * @return int
     */
    public function compareTo($value): int
    {
        $decimal = static::create($value);
        $scale = max($this->precision(), $decimal->precision());

        return bccomp($this, $decimal, $scale);
    }

    /**
     * Add $value to this Decimal and return the sum as a new Decimal.
     *
     * @param string|int|static $value
     * @param int|null $scale
     *
     * @return static
     */
    public function add($value, ?int $scale = null)
    {
        $decimal = static::create($value);
        $scale = static::resultPrecision($this, $decimal, $scale);

        return new static(bcadd($this, $decimal, $scale));
    }

    /**
     * Return an appropriate scale for an arithmetic operation on two Decimals.
     *
     * If $scale is specified and is a valid positive integer, return it.
     * Otherwise, return the higher of the scales of the operands.
     *
     * @param static $a
     * @param static $b
     * @param int|null $scale
     *
     * @return int
     */
    protected static function resultPrecision($a, $b, ?int $scale = null): int
    {
        if ($scale === null) {
            $scale = max($a->precision(), $b->precision());
        }

        return $scale;
    }

    /**
     * Subtract $value from this Decimal and return the difference as a new
     * Decimal.
     *
     * @param string|int|static $value
     * @param int|null $scale
     *
     * @return static
     */
    public function subtract($value, ?int $scale = null)
    {
        $decimal = static::create($value);
        $scale = static::resultPrecision($this, $decimal, $scale);

        return new static(bcsub($this, $decimal, $scale));
    }

    /**
     * Does not remove precision - keeps trailing zeroes.
     *
     * @return string
     */
    public function toStringWithPrecision(): string
    {
        $decimalPart = $this->decimalPart !== '' ? '.' . str_pad($this->decimalPart, $this->precision, '0') : '';

        return ($this->negative ? '-' : '') . $this->integerPart . $decimalPart;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function trimDecimals(string $value): string
    {
        return rtrim($value, '0') ?: '';
    }

    /**
     * Signum
     *
     * @return int 0 if zero, -1 if negative, or 1 if positive.
     */
    public function signum(): int
    {
        if ($this->isZero()) {
            return 0;
        }

        return $this->negative ? -1 : 1;
    }

    /**
     * Absolute
     *
     * @return static The absolute (positive) value of this decimal.
     */
    public function abs()
    {
        return $this->copy($this->integerPart, $this->decimalPart, false);
    }

    /**
     * @return bool
     */
    public function isZero(): bool
    {
        return $this->integerPart === 0 && $this->decimalPart === '';
    }

    /**
     * Return some approximation of this Decimal as a PHP native float.
     *
     * Due to the nature of binary floating-point, some valid values of Decimal
     * will not have any finite representation as a float, and some valid
     * values of Decimal will be out of the range handled by floats.
     *
     * @return float
     */
    public function toFloat(): float
    {
        return (float)$this->toString();
    }

    /**
     * String representation.
     *
     * This method is equivalent to a cast to string.
     *
     * This method should not be used as a canonical representation of this
     * decimal, because values can be represented in more than one way. However,
     * this method does guarantee that a decimal instantiated by its output with
     * the same precision will be exactly equal to this decimal.
     *
     * @return string the value of this decimal represented exactly, in either
     *                fixed or scientific form, depending on the value.
     */
    public function toString(): string
    {
        $decimalPart = $this->decimalPart !== '' ? '.' . $this->decimalPart : '';

        return ($this->negative ? '-' : '') . $this->integerPart . $decimalPart;
    }

    /**
     * Return a basic string representation of this Decimal.
     *
     * The output of this method is guaranteed to yield exactly the same value
     * if fed back into the Decimal constructor.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Get the printable version of this object
     *
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            'value' => $this->toString(),
            'precision' => $this->precision,
        ];
    }

    /**
     * Separates int and decimal parts and adds them to the state.
     *
     * - Removes leading 0 on int part
     * - '0.00001' can also come in as '1.0E-5'
     *
     * @param string $value
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function setValue(string $value): void
    {
        if (!preg_match('#e#i', $value)) {
            $separatorPos = strpos($value, '.');
            if ($separatorPos !== false) {
                $before = (int)substr($value, 0, $separatorPos);
                $after = substr($value, $separatorPos + 1);
            } else {
                $before = (int)$value;
                $after = '';
            }

            $this->negative = $before < 0 || $before === 0 && $after !== '' && strpos($value, '-') === 0;
            $this->integerPart = abs($before);
            $this->decimalPart = $this->trimDecimals($after);

            return;
        }

        $pattern = '/^(-?)(\d+(?:' . static::RADIX_MARK . '\d*)?|' .
            '[' . static::RADIX_MARK . ']' . '\d+)' . static::EXP_MARK . '(-?\d*)?$/i';
        preg_match($pattern, $value, $matches);
        if (!$matches) {
            throw new InvalidArgumentException('Invalid value/notation: ' . $value);
        }

        $negativeChar = $matches[1];
        $value = (float)$matches[2];
        $exp = (int)$matches[3];

        if ($exp < 0) {
            $this->integerPart = 0;
            $this->decimalPart = str_repeat('0', -$exp - 1) . $value;
        } else {
            $this->integerPart = (int)($value * pow(10, $exp));
            $this->decimalPart = '';
        }

        $this->negative = $negativeChar === '-';
    }
}

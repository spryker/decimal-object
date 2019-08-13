<?php
namespace Spryker\Decimal;

class Decimal
{
    public const DEFAULT_PRECISION = 28;

    /**
     * Internal value.
     *
     * TODO: separate into digits,exponent,negative? or int,float,negative
     *
     * @var string
     */
    protected $value;

    /**
     * @var int
     */
    protected $precision;

    /**
     * @param string|int|static $value
     * @param int $precision
     */
    public function __construct($value, int $precision = Decimal::DEFAULT_PRECISION)
    {
        if (!is_string($value)) {
            $value = (string)$value;
        }

        $this->value = $value;
        $this->precision = $precision;
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
        $scale = max($this->getScale(), $decimal->getScale());

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
        $scale = static::resultScale($this, $decimal, $scale);

        return new static(bcadd($this, $decimal, $scale));
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
        $scale = static::resultScale($this, $decimal, $scale);

        return new static(bcsub($this, $decimal, $scale));
    }

    /**
     * Trims trailing zeroes.
     *
     * @return static A copy of this decimal without trailing zeroes.
     */
    public function trim()
    {
    }

    /**
     * Signum
     *
     * @return int 0 if zero, -1 if negative, or 1 if positive.
     */
    public function signum(): int
    {
    }

    /**
     * Absolute
     *
     * @return static The absolute (positive) value of this decimal.
     */
    public function abs()
    {
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
        return (float)$this->value;
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
        return (string)$this->value;
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
            'value' => $this->value,
            'precision' => $this->precision,
        ];
    }
}

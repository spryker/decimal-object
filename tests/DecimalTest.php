<?php

namespace Spryker\Decimal\Test;

use LogicException;
use PHPUnit\Framework\TestCase;
use Spryker\Decimal\Decimal;

class DecimalTest extends TestCase
{
    /**
     * @return void
     */
    public function testNewObject(): void
    {
        $value = '1.1';
        $decimal = new Decimal($value);
        $result = $decimal->toString();
        $this->assertSame($value, $result);

        $value = 2;
        $decimal = new Decimal($value);
        $result = $decimal->toString();
        $this->assertSame('2', $result);

        $value = 2.2;
        $decimal = new Decimal($value);
        $result = $decimal->toString();
        $this->assertSame('2.2', $result);

        $value = -23;
        $decimal = new Decimal($value);
        $result = $decimal->toString();
        $this->assertSame('-23', $result);
    }

    /**
     * @return void
     */
    public function testNewObjectExponent(): void
    {
        $value = 0.000001;
        $decimal = new Decimal($value);
        $result = $decimal->toString();
        $this->assertSame('0.000001', $result);

        $value = -0.000001;
        $decimal = new Decimal($value);
        $result = $decimal->toString();
        $this->assertSame('-0.000001', $result);
    }

    /**
     * @dataProvider baseProvider
     *
     * @param mixed $value
     * @param string $expected
     *
     * @return void
     */
    public function testCreate($value, string $expected): void
    {
        $decimal = Decimal::create($value);
        $this->assertSame($expected, (string)$decimal);
    }

    /**
     * @return array
     */
    public function baseProvider(): array
    {
        return [
            [50, '50'],
            [-25000, '-25000'],
            [0.00001, '0.00001'],
            [-0.000003, '-0.000003'],
            ['.0189', '0.0189'],
            ['-.3', '-0.3'],
            ['-5.000067', '-5.000067'],
            ['+5.000067', '5.000067'],
            ['0000005', '5'],
            ['  0.0   ', '0'],
            ['6.22e8', '622000000'],
            ['6.22e18', '6220000000000000000'],
            [PHP_INT_MAX, (string)PHP_INT_MAX],
            [-PHP_INT_MAX, '-' . (string)PHP_INT_MAX],
            [new Decimal('-12.375'), '-12.375'],
            ['0000', '0'],
            ['-0', '0'],
            ['+0', '0'],
        ];
    }

    /**
     * @dataProvider zeroProvider
     *
     * @param mixed $value
     * @param bool $expected
     *
     * @return void
     */
    public function testIsZero($value, bool $expected): void
    {
        $decimal = Decimal::create($value);
        $this->assertSame($expected, $decimal->isZero());
    }

    /**
     * @return array
     */
    public function zeroProvider(): array
    {
        return [
            [5, false],
            [0.00001, false],
            [-0.000003, false],
            [new Decimal('0'), true],
            [0, true],
            [0.0, true],
            ['0000', true],
            ['-0', true],
            ['+0', true],
        ];
    }

    /**
     * @dataProvider compareZeroProvider
     *
     * @param mixed $input
     * @param int $expected
     *
     * @return void
     */
    public function testIsPositive($input, int $expected): void
    {
        $dec = new Decimal($input);
        $this->assertSame($expected > 0, $dec->isPositive());
    }

    /**
     * @dataProvider compareZeroProvider
     *
     * @param mixed $input
     * @param int $expected
     *
     * @return void
     */
    public function testIsNegative($input, int $expected): void
    {
        $dec = new Decimal($input);
        $this->assertSame($expected < 0, $dec->isNegative());
    }

    /**
     * @return array
     */
    public function compareZeroProvider(): array
    {
        return [
            [0, 0],
            [1, 1],
            [-1, -1],
            [0.0, 0],
            ['0', 0],
            ['1', 1],
            ['-1', -1],
            ['00000', 0],
            ['0.0', 0],
            ['0.00001', 1],
            ['1e-20', 1],
            ['-1e-20', -1],
        ];
    }

    /**
     * @dataProvider precisionProvider
     *
     * @param mixed $input
     * @param int $expected
     *
     * @return void
     */
    public function testPrecision($input, int $expected): void
    {
        $this->markTestSkipped('TBD');

        $decimal = Decimal::create($input);
        $this->assertSame($expected, $decimal->precision());
    }

    /**
     * @return array
     */
    public function precisionProvider(): array
    {
        return [
            [0, 0],
            [1, 0],
            [-1, 0],
            ['12.375', 3],
            ['-0.7', 1],
            ['6.22e23', 0],
            ['1e-10', 10],
        ];
    }

    /**
     * @return void
     */
    public function testToString(): void
    {
        $value = -23;
        $decimal = new Decimal($value);

        $result = (string)$decimal;
        $this->assertSame('-23', $result);
    }

    /**
     * @return void
     */
    public function testToStringWithPrecision(): void
    {
        $value = '-2.0300000000000000000000000000';
        $decimal = new Decimal($value);

        $result = $decimal->toStringWithPrecision();
        $this->assertSame($value, $result);
    }

    /**
     * @return void
     */
    public function testToFloat(): void
    {
        $value = '-23.44';
        $decimal = new Decimal($value);

        $result = $decimal->toFloat();
        $this->assertSame(-23.44, $result);
    }

    /**
     * @return void
     */
    public function testToInt(): void
    {
        $value = '-23.74';
        $decimal = new Decimal($value);

        $result = $decimal->toInt();
        $this->assertSame(-23, $result);
    }

    /**
     * @return void
     */
    public function testAbsolute(): void
    {
        $value = '-23.44';
        $decimal = new Decimal($value);

        $result = $decimal->absolute();
        $this->assertSame('23.44', (string)$result);
    }

    /**
     * @return void
     */
    public function testNegation(): void
    {
        $value = '-23.44';
        $decimal = new Decimal($value);

        $result = $decimal->negation();
        $this->assertSame('23.44', (string)$result);

        $again = $result->negation();
        $this->assertSame($value, (string)$again);
    }

    /**
     * @return void
     */
    public function testIsNegativeBasic(): void
    {
        $value = '-23.44';
        $decimal = new Decimal($value);
        $this->assertTrue($decimal->isNegative());

        $value = '23.44';
        $decimal = new Decimal($value);
        $this->assertFalse($decimal->isNegative());

        $value = '0';
        $decimal = new Decimal($value);
        $this->assertFalse($decimal->isNegative());
    }

    /**
     * @return void
     */
    public function testIsPositiveBasic(): void
    {
        $value = '-23.44';
        $decimal = new Decimal($value);
        $this->assertFalse($decimal->isPositive());

        $value = '23.44';
        $decimal = new Decimal($value);
        $this->assertTrue($decimal->isPositive());

        $value = '0';
        $decimal = new Decimal($value);
        $this->assertFalse($decimal->isPositive());
    }

    /**
     * @return void
     */
    public function testEquals(): void
    {
        $value = '1.1';
        $decimalOne = new Decimal($value);

        $value = '1.10';
        $decimalTwo = new Decimal($value);

        $result = $decimalOne->equals($decimalTwo);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider compareProvider
     *
     * @param mixed $a
     * @param mixed $b
     * @param int $expected
     *
     * @return void
     */
    public function testGreaterThan($a, $b, int $expected): void
    {
        $dec = new Decimal($a);
        $this->assertSame($expected > 0, $dec->greaterThan($b));
    }

    /**
     * @dataProvider compareProvider
     *
     * @param mixed $a
     * @param mixed $b
     * @param int $expected
     *
     * @return void
     */
    public function testLessThan($a, $b, int $expected): void
    {
        $dec = new Decimal($a);
        $this->assertSame($expected < 0, $dec->lessThan($b));
    }

    /**
     * @dataProvider compareProvider
     *
     * @param mixed $a
     * @param mixed $b
     * @param int $expected
     *
     * @return void
     */
    public function testGreaterEquals($a, $b, int $expected): void
    {
        $dec = new Decimal($a);
        $this->assertSame($expected >= 0, $dec->greatherThanOrEquals($b));
    }

    /**
     * @dataProvider compareProvider
     *
     * @param mixed $a
     * @param mixed $b
     * @param int $expected
     *
     * @return void
     */
    public function testLessEquals($a, $b, int $expected): void
    {
        $dec = new Decimal($a);
        $this->assertSame($expected <= 0, $dec->lessThanOrEquals($b));
    }

    /**
     * @return array
     */
    public function compareProvider(): array
    {
        return [
            [0, 0, 0],
            [1, 0, 1],
            [-1, 0, -1],
            ['12.375', '12.375', 0],
            ['12.374', '12.375', -1],
            ['12.376', '12.375', 1],
            ['6.22e23', '6.22e23', 0],
            ['1e-10', '1e-9', -1],
        ];
    }

    /**
     * @return void
     */
    public function testAdd(): void
    {
        $value = '1.1';
        $decimalOne = new Decimal($value);

        $value = '1.2';
        $decimalTwo = new Decimal($value);

        $result = $decimalOne->add($decimalTwo);
        $this->assertSame('2.3', (string)$result);
    }

    /**
     * @return void
     */
    public function testSubtract(): void
    {
        $value = '0.1';
        $decimalOne = new Decimal($value);

        $value = '0.01';
        $decimalTwo = new Decimal($value);

        $result = $decimalOne->subtract($decimalTwo);
        $this->assertSame('0.09', (string)$result);
    }

    /**
     * @dataProvider multiplicationProvider
     *
     * @param mixed $a
     * @param mixed $b
     * @param int|null $precision
     * @param string $expected
     *
     * @return void
     */
    public function testMultiply($a, $b, ?int $precision, string $expected): void
    {
        $dec = new Decimal($a);
        $this->assertSame($expected, (string)$dec->multiply($b, $precision));
    }

    /**
     * @return array
     */
    public function multiplicationProvider(): array
    {
        return [
            ['0', '0', null, '0'],
            ['1', '10', null, '10'],
            ['1000', '10', null, '10000'],
            ['-10', '10', null, '-100'],
            ['10', '-10', null, '-100'],
            ['10', '10', null, '100'],
            ['0.1', '1', null, '0.1'],
            ['0.1', '0.01', null, '0.001'],
            ['-0.001', '0.01', null, '-0.00001'],
            ['0', '0', 3, '0'],
            ['9', '0.001', 3, '0.009'],
            ['9', '0.001', 0, '0'],
            ['1e-10', '28', null, '0.0000000028'],
            ['1e-10', '-1e-10', null, '-0.00000000000000000001'],
            ['1e-10', '-1e-10', 20, '-0.00000000000000000001'],
            ['1e-10', '-1e-10', 19, '0'],
        ];
    }

    /**
     * @dataProvider divisionProvider
     *
     * @param mixed $a
     * @param mixed $b
     * @param int|null $precision
     * @param string $expected
     *
     * @return void
     */
    public function testDivide($a, $b, ?int $precision, string $expected): void
    {
        $dec = new Decimal($a);
        $this->assertSame($expected, (string)$dec->divide($b, $precision));
    }

    /**
     * @return void
     */
    public function testDivideByZero(): void
    {
        $dec = new Decimal(1);

        $this->expectException(LogicException::class);

        $dec->divide(0);
    }

    /**
     * @return array
     */
    public function divisionProvider(): array
    {
        return [
            ['0', '1', null, '0'],
            ['1', '1', null, '1'],
            ['0', '1e6', null, '0'],
            [1, 10, 1, '0.1'],
            ['1000', '10', null, '100'],
            ['-10', '10', null, '-1'],
            ['10', '-10', null, '-1'],
            ['10', '10', null, '1'],
            ['0.1', '1', null, '0.1'],
            ['0.1', '0.01', null, '10'],
            ['-0.001', '0.01', 1, '-0.1'],
            ['1', '3', 3, '0.333'],
            ['1', '3', 0, '0'],
            //['6.22e23', '2', null, '311000000000000000000000'],
            //['6.22e23', '-1', null, '-622000000000000000000000'],
            //['1e-10', 3, null, '0'],
            //['1e-10', 3, 11, '0.00000000003'],
            //['1e-10', 3, 12, '0.000000000033'],
        ];
    }

    /**
     * @return void
     */
    public function testDebugInfo(): void
    {
        $value = '1.1';
        $decimal = new Decimal($value);

        $result = $decimal->__debugInfo();
        $expected = [
            'value' => $value,
            'precision' => Decimal::DEFAULT_PRECISION,
        ];
        $this->assertEquals($expected, $result);
    }
}

<?php

namespace Spryker\Decimal\Test;

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
        $this->assertSame($expected, $decimal->isZero()); //'value `' . $value . '` is `' . (int)$decimal->isZero() . '` expected `' . (int)$expected . '`'
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
    public function testAbs(): void
    {
        $value = '-23.44';
        $decimal = new Decimal($value);

        $result = $decimal->abs();
        $this->assertSame('23.44', (string)$result);
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

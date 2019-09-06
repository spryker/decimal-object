<?php

namespace Spryker\DecimalObject\Test;

use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use Spryker\DecimalObject\Decimal;

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
    public function testNewObjectScientific(): void
    {
        $value = '2.2e-6';
        $decimal = new Decimal($value);
        $result = $decimal->toString();
        $this->assertSame('0.0000022', $result);

        $this->assertSame(7, $decimal->scale());
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
            [0.00001, '0.000010'], // !
            [-0.000003, '-0.0000030'], // !
            ['.0189', '0.0189'],
            ['-.3', '-0.3'],
            ['-5.000067', '-5.000067'],
            ['+5.000067', '5.000067'],
            ['0000005', '5'],
            ['  0.0   ', '0.0'],
            ['6.22e8', '622000000'],
            ['6.22e18', '6220000000000000000'],
            [PHP_INT_MAX, (string)PHP_INT_MAX],
            [-PHP_INT_MAX, '-' . PHP_INT_MAX],
            [Decimal::create('-12.375'), '-12.375'],
            ['0000', '0'],
            ['-0', '0'],
            ['+0', '0'],
        ];
    }

    /**
     * @dataProvider truncateProvider
     *
     * @param mixed $input
     * @param int $scale
     * @param string $expected
     *
     * @return void
     */
    public function testTruncate($input, int $scale, string $expected): void
    {
        $decimal = Decimal::create($input);
        $this->assertSame($expected, (string)$decimal->truncate($scale));
    }

    /**
     * @return array
     */
    public function truncateProvider(): array
    {
        return [
            [0, 0, '0'],
            [1, 0, '1'],
            [-1, 0, '-1'],
            ['12.375', 2, '12.37'],
            ['12.374', 0, '12'],
            ['-12.376', 1, '-12.3'],
        ];
    }

    /**
     * @dataProvider integerProvider
     *
     * @param mixed $value
     * @param bool $expected
     *
     * @return void
     */
    public function testIsInteger($value, bool $expected): void
    {
        $decimal = Decimal::create($value);
        $this->assertSame($expected, $decimal->isInteger());
    }

    /**
     * @return array
     */
    public function integerProvider(): array
    {
        return [
            [5, true],
            [0.00001, false],
            [-0.000003, false],
            [Decimal::create('0'), true],
            [0, true],
            [0.0, true],
            ['0000', true],
            ['-0', true],
            ['+0', true],
            [-121211, true],
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
            [Decimal::create('0'), true],
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
        $decimal = Decimal::create($input);
        $this->assertSame($expected > 0, $decimal->isPositive());
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
        $decimal = Decimal::create($input);
        $this->assertSame($expected < 0, $decimal->isNegative());
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
     * @dataProvider scaleProvider
     *
     * @param mixed $input
     * @param int $expected
     *
     * @return void
     */
    public function testScale($input, int $expected): void
    {
        $decimal = Decimal::create($input);
        $this->assertSame($expected, $decimal->scale());
    }

    /**
     * @return array
     */
    public function scaleProvider(): array
    {
        return [
            [0, 0],
            [1, 0],
            [-1, 0],
            ['120', 0],
            ['12.375', 3],
            ['-0.7', 1],
            ['6.22e23', 0],
            ['1e-10', 10],
            ['-2.3e-10', 11], // 0.00000000023
        ];
    }

    /**
     * @return void
     */
    public function testToScientific(): void
    {
        $decimal = Decimal::create(-23);
        $this->assertSame('-2.3e1', $decimal->toScientific());
        $revertedDecimal = Decimal::create($decimal->toScientific());
        $this->assertSame('-23', (string)$revertedDecimal);

        $decimal = Decimal::create('1.000');
        $this->assertSame('1.000e0', $decimal->toScientific());
        $revertedDecimal = Decimal::create($decimal->toScientific());
        $this->assertSame('1.000', (string)$revertedDecimal);

        $decimal = Decimal::create('-22.345');
        $this->assertSame('-2.2345e1', $decimal->toScientific());
        $revertedDecimal = Decimal::create($decimal->toScientific());
        $this->assertSame('-22.345', (string)$revertedDecimal);

        $decimal = Decimal::create('30022.0345');
        $this->assertSame('3.00220345e4', $decimal->toScientific());
        $revertedDecimal = Decimal::create($decimal->toScientific());
        $this->assertSame('30022.0345', (string)$revertedDecimal);

        $decimal = Decimal::create('-0.00230');
        $this->assertSame('-2.30e-3', $decimal->toScientific());
        $revertedDecimal = Decimal::create($decimal->toScientific());
        $this->assertSame('-0.00230', (string)$revertedDecimal);
    }

    /**
     * @return void
     */
    public function testToString(): void
    {
        $value = -23;
        $decimal = Decimal::create($value);

        $result = (string)$decimal;
        $this->assertSame('-23', $result);
    }

    /**
     * @return void
     */
    public function testTrim(): void
    {
        $value = '-2.0300000000000000000000000000';
        $decimal = Decimal::create($value);
        $this->assertSame(28, $decimal->scale());

        $trimmed = $decimal->trim();
        $this->assertSame('-2.03', (string)$trimmed);
        $this->assertSame(2, $trimmed->scale());

        $value = '2000';
        $decimal = Decimal::create($value);

        $trimmed = $decimal->trim();
        $this->assertSame('2000', (string)$trimmed);
        $this->assertSame(0, $trimmed->scale());
    }

    /**
     * @return void
     */
    public function testToFloat(): void
    {
        $value = '-23.44';
        $decimal = Decimal::create($value);

        $result = $decimal->toFloat();
        $this->assertSame(-23.44, $result);
    }

    /**
     * @return void
     */
    public function testToInt(): void
    {
        $value = '-23.74';
        $decimal = Decimal::create($value);

        $result = $decimal->toInt();
        $this->assertSame(-23, $result);
    }

    /**
     * @return void
     */
    public function testMod(): void
    {
        $value = '7';
        $decimal = Decimal::create($value);

        $result = $decimal->mod(2);
        $this->assertSame('1', (string)$result);
    }

    /**
     * @return void
     */
    public function testPow(): void
    {
        $value = '8';
        $decimal = Decimal::create($value);

        $result = $decimal->pow(2);
        $this->assertSame('64', (string)$result);
    }

    /**
     * @return void
     */
    public function testSqrt(): void
    {
        $value = '64';
        $decimal = Decimal::create($value);

        $result = $decimal->sqrt();
        $this->assertSame('8', (string)$result);

        $value = '18';
        $decimal = Decimal::create($value);

        $result = $decimal->sqrt(5);
        $this->assertSame('4.24264', (string)$result);

        $value = '18.000000';
        $decimal = Decimal::create($value);

        $result = $decimal->sqrt();
        $this->assertSame('4.242640', (string)$result);
    }

    /**
     * @return void
     */
    public function testAbsolute(): void
    {
        $value = '-23.44';
        $decimal = Decimal::create($value);

        $result = $decimal->absolute();
        $this->assertSame('23.44', (string)$result);
    }

    /**
     * @return void
     */
    public function testNegate(): void
    {
        $value = '-23.44';
        $decimal = Decimal::create($value);

        $result = $decimal->negate();
        $this->assertSame('23.44', (string)$result);

        $again = $result->negate();
        $this->assertSame($value, (string)$again);
    }

    /**
     * @return void
     */
    public function testIsNegativeBasic(): void
    {
        $value = '-23.44';
        $decimal = Decimal::create($value);
        $this->assertTrue($decimal->isNegative());

        $value = '23.44';
        $decimal = Decimal::create($value);
        $this->assertFalse($decimal->isNegative());

        $value = '0';
        $decimal = Decimal::create($value);
        $this->assertFalse($decimal->isNegative());
    }

    /**
     * @return void
     */
    public function testIsPositiveBasic(): void
    {
        $value = '-23.44';
        $decimal = Decimal::create($value);
        $this->assertFalse($decimal->isPositive());

        $value = '23.44';
        $decimal = Decimal::create($value);
        $this->assertTrue($decimal->isPositive());

        $value = '0';
        $decimal = Decimal::create($value);
        $this->assertFalse($decimal->isPositive());
    }

    /**
     * @return void
     */
    public function testEquals(): void
    {
        $value = '1.1';
        $decimalOne = Decimal::create($value);

        $value = '1.10';
        $decimalTwo = Decimal::create($value);

        $result = $decimalOne->equals($decimalTwo);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider roundProvider
     *
     * @param mixed $value
     * @param int $scale
     * @param string $expected
     *
     * @return void
     */
    public function testRound($value, int $scale, string $expected): void
    {
        $decimal = Decimal::create($value);
        $this->assertSame($expected, (string)$decimal->round($scale));
    }

    /**
     * @return array
     */
    public function roundProvider(): array
    {
        return [
            [0, 0, '0'],
            [1, 0, '1'],
            [-1, 0, '-1'],
            ['12.375', 1, '12.4'],
            ['12.374', 2, '12.37'],
            ['-13.574', 0, '-14'],
            [13.4999, 0, '13'],
        ];
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
        $decimal = Decimal::create($a);
        $this->assertSame($expected > 0, $decimal->greaterThan($b));
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
        $decimal = Decimal::create($a);
        $this->assertSame($expected < 0, $decimal->lessThan($b));
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
        $decimal = Decimal::create($a);
        $this->assertSame($expected >= 0, $decimal->greatherThanOrEquals($b));
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
        $decimal = Decimal::create($a);
        $this->assertSame($expected <= 0, $decimal->lessThanOrEquals($b));
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
        $decimalOne = Decimal::create($value);

        $value = '1.2';
        $decimalTwo = Decimal::create($value);

        $result = $decimalOne->add($decimalTwo);

        $this->assertSame('2.3', (string)$result);
        $this->assertSame(1, $result->scale());
    }

    /**
     * @return void
     */
    public function testSubtract(): void
    {
        $value = '0.1';
        $decimalOne = Decimal::create($value);

        $value = '0.01';
        $decimalTwo = Decimal::create($value);

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
        $decimal = Decimal::create($a);
        $this->assertSame($expected, (string)$decimal->multiply($b, $precision));
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
            ['0', '0', 3, '0.000'],
            ['9', '0.001', 3, '0.009'],
            ['9', '0.001', 0, '0'],
            ['1e-10', '28', null, '0.0000000028'],
            ['1e-10', '-1e-10', null, '-0.00000000000000000001'],
            ['1e-10', '-1e-10', 20, '-0.00000000000000000001'],
            ['1e-10', '-1e-10', 19, '0.0000000000000000000'],
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
        $decimal = Decimal::create($a);
        $this->assertSame($expected, (string)$decimal->divide($b, $precision));
    }

    /**
     * @return void
     */
    public function testDivideByZero(): void
    {
        $decimal = Decimal::create(1);

        $this->expectException(LogicException::class);

        $decimal->divide(0);
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
            ['0.1', '0.01', null, '10.00'],
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
        $decimal = Decimal::create($value);

        $result = $decimal->__debugInfo();
        $expected = [
            'value' => $value,
            //'precision' => 2,
            'scale' => 1,
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * @return void
     */
    public function testPrecisionLossProtection(): void
    {
        $a = Decimal::create('0.1', 50);
        $this->assertSame(50, $a->scale());

        $b = Decimal::create($a);
        $this->assertSame(50, $b->scale());

        $c = Decimal::create($b, 6); // Not 50 if manually overwritten
        $this->assertSame(6, $c->scale());

        $d = Decimal::create($c, 64);
        $this->assertSame(64, $d->scale());
    }

    /**
     * @return void
     */
    public function testPrecisionLossFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Loss of precision detected');

        Decimal::create('0.123', 2);
    }
}

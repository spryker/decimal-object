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
    public function testEquals(): void
    {
        $this->markTestSkipped('TODO');

        $value = '1.1';
        $decimalOne = new Decimal($value);

        $value = '1.1';
        $decimalTwo = new Decimal($value);

        $result = $decimalOne->equals($decimalTwo);
        $this->assertTrue($result);
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

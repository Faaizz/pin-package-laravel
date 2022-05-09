<?php

namespace Faaizz\PinGenerator\Tests\Unit;

use Exception;
use Faaizz\PinGenerator\Generator;
use Faaizz\PinGenerator\Tests\TestCase;
use Mockery;

class GeneratorTest extends TestCase
{
    public function randumNumIntervalsProvider(): array
    {
        return [
            '4 digits valid limits' => [
                'digits' => 4,
                'upLimit' => 9999,
                'lowLimit' => 0,
                'expectFail' => false,
            ],
            '1 digit valid limits' => [
                'digits' => 1,
                'upLimit' => 9,
                'lowLimit' => 0,
                'expectFail' => false,
            ],
            '8 digits valid limits' => [
                'digits' => 8,
                'upLimit' => 99999999,
                'lowLimit' => 0,
                'expectFail' => false,
            ],
        ];
    }

    /**
     * @dataProvider randumNumIntervalsProvider
     */
    public function testRandumNumIntervals(int $digits, int $upLimit, int $lowLimit, bool $expectFail)
    {
        config(['pingenerator.digits' => $digits]);

        $gen = new Generator();

        $randomNum = $gen->randomNum();

        $fails = !( ($lowLimit <= $randomNum) && ($randomNum <= $upLimit) );

        $this->assertEquals($expectFail, $fails);
    }

    public function checkObviousProvider(): array
    {
        return [
            '1234 is obvious' => [
                1234,
                [1234],
                true,
            ],
            '4564 not obvious' => [
                4564,
                [1234, 1111],
                false,
            ],
            '1111 is obvious' => [
                1111,
                [0000, 1111],
                true,
            ],
            '7682 not obvious' => [
                7682,
                [1234, 1111, 7777],
                false,
            ],
        ];
    }

    /**
     * @dataProvider checkObviousProvider
     */
    public function testCheckObvious($pin, $obviousNumbers, $isObvious)
    {
        config(['pingenerator.obvious_numbers' => $obviousNumbers]);

        $gen = new Generator();

        $this->assertEquals($isObvious, $gen->checkObvious($pin));
    }

    public function formatProvider(): array
    {
        return [
            'format 3 digits' => [
                3,
                '001',
                1,
            ],
            'format 8 digits' => [
                8,
                '34534320',
                34534320,
            ],
            'format 4 digits' => [
                4,
                '0606',
                606,
            ],
        ];
    }

    /**
     * @dataProvider formatProvider
     */
    public function testFormat($numDigits, $expected, $pin)
    {
        config(['pingenerator.digits' => $numDigits]);

        $gen = new Generator();
        $this->assertEquals($expected, $gen->format($pin));
    }

    public function generatePinProvider(): array
    {
        return [
            '2 digits' => [
                2,
                false,
            ],
            '2 digits fail' => [
                2,
                true,
            ],
            '3 digits' => [
                3,
                false,
            ],
            '4 digits' => [
                4,
                false,
            ],
            '5 digits' => [
                5,
                false,
            ],
            '6 digits' => [
                6,
                false,
            ],
            '6 digits fail' => [
                6,
                true,
            ],
            '7 digits' => [
                7,
                false,
            ],
            '8 digits' => [
                8,
                false,
            ],
        ];
    }

    /**
     * @dataProvider generatePinProvider
     */
    public function testGeneratePin($numDigits, $expectFail)
    {
        config(['pingenerator.digits' => $numDigits]);

        $gen = Mockery::mock(Generator::class . '[randomNum,checkObvious]');

        $this->instance(
            Generator::class,
            $gen
        );

        if ($expectFail) {
            $gen->shouldReceive('randomNum')
                    ->atLeast()
                    ->times(100)
                    ->andReturn(0000);

            $gen->shouldReceive('checkObvious')
                    ->atLeast()
                    ->times(100)
                    ->passthru();

            $this->expectException(Exception::class);
            $gen->generatePin();

            return;
        } else {
            $gen->shouldReceive('randomNum')
                    ->passthru();
            $gen->shouldReceive('checkObvious')
                    ->passthru();
        }

        $this->assertEquals($numDigits, strlen($gen->generatePin()));
    }
}

<?php

namespace Faaizz\PinGenerator\Tests\Unit;

use Faaizz\PinGenerator\Generator;
use Faaizz\PinGenerator\Tests\TestCase;

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
            '4 digits invalid limits' => [
                'digits' => 4,
                'upLimit' => 900,
                'lowLimit' => 0,
                'expectFail' => true,
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

    public function getPinProvider(): array
    {
        return [
            '2 digits' => [2],
            '3 digits' => [3],
            '4 digits' => [4],
            '5 digits' => [5],
            '6 digits' => [6],
            '7 digits' => [7],
            '8 digits' => [8],
        ];
    }

    /**
     * @dataProvider getPinProvider
     */
    public function testGetPin($numDigits)
    {
        config(['pingenerator.digits' => $numDigits]);

        $gen = new Generator();
        $this->assertEquals($numDigits, strlen($gen->getPin()));
    }
}

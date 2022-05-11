<?php

namespace Faaizz\PinGenerator\Tests\Feature\Facades;

use Faaizz\PinGenerator\Facades\Generator;
use Faaizz\PinGenerator\Tests\TestCase;

class GeneratorTest extends TestCase
{
    public function generatePinProvider(): array
    {
        return [
            'generate 4 digit PIN withno restrictions' => [
                4,
                [],
            ],
            'generate 4 digit PIN without 1111, 5555' => [
                4,
                [1111, 5555],
            ],
            'generate 4 digit PIN without 4367, 9900, 5555' => [
                4,
                [4367, 9900, 5555],
            ],
            'generate 4 digit PIN without 1111, 2222, 3333, 4444, 5555, 6666, 7777, 8888, 9999, 0000' => [
                4,
                [1111, 2222, 3333, 4444, 5555, 6666, 7777, 8888, 9999, 0000],
            ],
            'generate 8 digit PIN with no restrictions' => [
                8,
                [],
            ],
            'generate 1 digit PIN without 1, 2, 3, 4' => [
                1,
                [1, 2, 3, 4],
            ],
        ];
    }

    /**
     * @dataProvider generatePinProvider
     */
    public function testGeneratePin($numDigits, $obviousNumbers)
    {
        config(['pingenerator.digits' => $numDigits]);
        config(['pingenerator.obvious_numbers' => $obviousNumbers]);

        $pin = Generator::generatePin();
        $pinNum = intval($pin);

        $this->assertEquals($numDigits, strlen($pin));
        $this->assertFalse(in_array($pinNum, $obviousNumbers));
    }
}

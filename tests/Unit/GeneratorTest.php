<?php

namespace Faaizz\PinGenerator\Tests\Unit;

use Exception;
use Faaizz\PinGenerator\Generator;
use Faaizz\PinGenerator\Models\Pin;
use Faaizz\PinGenerator\Tests\TestCase;
use Faaizz\PinGenerator\Tests\Traits\AccessInaccessibleMethodsTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Mockery;

class GeneratorTest extends TestCase
{
    use AccessInaccessibleMethodsTrait;

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
    public function testRandumNumIntervals(int $digits, int $upLimit, int $lowLimit, bool $expectFail): void
    {
        config(['pingenerator.digits' => $digits]);

        $gen = new Generator();

        $randomNum = $this->invokeInaccessibleMethod($gen, 'randomNum', []);

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
    public function testCheckObvious($pin, $obviousNumbers, $isObvious): void
    {
        config(['pingenerator.obvious_numbers' => $obviousNumbers]);

        $gen = new Generator();

        $res = $this->invokeInaccessibleMethod($gen, 'checkObvious', [$pin]);

        $this->assertEquals($isObvious, $res);
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
    public function testFormat($numDigits, $expected, $pin): void
    {
        config(['pingenerator.digits' => $numDigits]);

        $gen = new Generator();
        $res = $this->invokeInaccessibleMethod($gen, 'format', [$pin]);
        $this->assertEquals($expected, $res);
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
    public function testGeneratePin($numDigits, $expectFail): void
    {
        config(['pingenerator.digits' => $numDigits]);

        $gen = Mockery::mock(Generator::class . '[randomNum,checkObvious]');
        $gen = $gen->shouldAllowMockingProtectedMethods();

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

    public function testClearPins()
    {
        $cacheTag = 'pingenerator_pins';
        config(['pingenerator.obvious_numbers' => []]);
        $gen = new Generator();

        $this->assertEquals(0, Pin::count());

        Pin::create(['pin' => 9876]);
        $this->assertEquals(1, Pin::count());

        $pinStr = $gen->generatePin();
        $pin = intval($pinStr);

        $this->assertTrue(Pin::where('pin', $pin)->count() > 0);

        $this->invokeInaccessibleMethod($gen, 'clearPins', []);
        $this->assertEquals(0, Pin::count());
    }

    public function checkExhaustedProvider(): array
    {
        return [
            '1 digit, 3 obvious numbers, 7 valid PINs' => [
                1,
                [2, 3, 4],
                7,
            ],
            '4 digits, 10 obvious numbers, 9990 valid PINs' => [
                4,
                [0000, 1111, 2222, 3333, 4444, 5555, 6666, 7777, 8888, 9999],
                9990,
            ],
        ];
    }

    /**
     * @dataProvider checkExhaustedProvider
     */
    public function testCheckExhausted($numDigits, $obviousNumbers, $validPins)
    {
        config(['pingenerator.digits' => $numDigits]);
        config(['pingenerator.obvious_numbers' => $obviousNumbers]);
        $gen = new Generator();

        $res = $this->invokeInaccessibleMethod($gen, 'checkExhausted', []);
        $this->assertFalse($res);

        $validPins = (10 ** $numDigits) - count($obviousNumbers);

        DB::beginTransaction();
        for ($idx = 0; $idx < $validPins; $idx++) {
            Pin::create(['pin' => $idx]);
        }
        DB::commit();

        $this->assertEquals($validPins, Pin::count());

        $res = $this->invokeInaccessibleMethod($gen, 'checkExhausted', []);
        $this->assertTrue($res);
    }

    public function testCheckExists()
    {
        $gen = new Generator();

        $pinNum = 1234;

        $res = $this->invokeInaccessibleMethod($gen, 'checkExists', [$pinNum]);
        $this->assertFalse($res);

        $pin = new Pin();
        $pin->pin = $pinNum;
        $pin->save();

        $res = $this->invokeInaccessibleMethod($gen, 'checkExists', [$pinNum]);
        $this->assertTrue($res);
    }

    public function countObviousPrecedingPinsProvider(): array
    {
        return [
            '3 preceding obvious numbers' => [
                1,
                [2, 3, 4, 5, 6, 7],
                5,
                3,
            ],
            '0 preceding obvious number' => [
                4,
                [879, 2398, 6590],
                90,
                0,
            ],
            '1 preceding obvious number' => [
                4,
                [879, 2398, 6590],
                1090,
                1,
            ],
            '11 preceding obvious number' => [
                4,
                [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 879, 2398, 6590],
                1190,
                11,
            ],
        ];
    }

    /**
     * @dataProvider countObviousPrecedingPinsProvider
     */
    public function testCountObviousPrecedingPins($numDigits, $obviousNumbers, $pin, $precedingObviousNumbers)
    {
        config(['pingenerator.digits' => $numDigits]);
        config(['pingenerator.obvious_numbers' => $obviousNumbers]);
        $gen = new Generator();

        $res = $this->invokeInaccessibleMethod($gen, 'countObviousPrecedingPins', [$pin]);

        $this->assertEquals($precedingObviousNumbers, $res);
    }

    public function testCheckAllPrecedingPinsEmitted()
    {
        $pin = 1534;

        config(['pingenerator.obvious_numbers' => []]);
        $gen = new Generator();

        $res = $this->invokeInaccessibleMethod($gen, 'checkAllPrecedingPinsEmitted', [$pin]);
        $this->assertFalse($res);

        for ($idx = 0; $idx < $pin; $idx++) {
            Pin::create(['pin' => $idx]);
        }

        $res = $this->invokeInaccessibleMethod($gen, 'checkAllPrecedingPinsEmitted', [$pin]);
        $this->assertTrue($res);
    }
}

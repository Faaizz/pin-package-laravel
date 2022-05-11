<?php

namespace Faaizz\PinGenerator\Tests\Unit;

use Exception;
use Faaizz\PinGenerator\Generator;
use Faaizz\PinGenerator\Tests\TestCase;
use Faaizz\PinGenerator\Tests\Traits\AccessInaccessibleMethodsTrait;
use Illuminate\Support\Facades\Cache;
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

    public function testCheckInCache()
    {
        $cacheTag = 'pingenerator_pins';
        config(['pingenerator.cache_tag' => $cacheTag]);
        $gen = new Generator();

        $pin = random_int(0, 9999);
        $res = $this->invokeInaccessibleMethod($gen, 'checkInCache', [$pin]);
        $this->assertFalse($res);


        $key = $cacheTag . $pin;
        Cache::tags($cacheTag)->put($key, $pin);

        $res = $this->invokeInaccessibleMethod($gen, 'checkInCache', [$pin]);
        $this->assertTrue($res);
    }

    public function testPutInCacheAndIncrementCount()
    {
        $cacheTag = 'pingenerator_pins';
        config(['pingenerator.cache_tag' => $cacheTag]);
        $gen = new Generator();

        $pin = random_int(0, 9999);

        $this->assertTrue(Cache::tags($cacheTag)->has('count'));
        $this->assertEquals(0, Cache::tags($cacheTag)->get('count'));

        $this->invokeInaccessibleMethod($gen, 'putInCacheAndIncrementCount', [$pin]);

        $key = $cacheTag . $pin;
        $this->assertTrue(Cache::tags($cacheTag)->has($key));
        $this->assertEquals(1, Cache::tags($cacheTag)->get('count'));
    }

    public function testClearCache()
    {
        $cacheTag = 'pingenerator_pins';
        config(['pingenerator.cache_tag' => $cacheTag]);
        $gen = new Generator();

        $this->assertTrue(Cache::tags($cacheTag)->has('count'));
        $this->assertEquals(0, Cache::tags($cacheTag)->get('count'));

        Cache::tags($cacheTag)->increment('count');
        $this->assertEquals(1, Cache::tags($cacheTag)->get('count'));

        $pinStr = $gen->generatePin();
        $pin = intval($pinStr);

        $key = $cacheTag . $pin;
        $this->assertTrue(Cache::tags($cacheTag)->has($key));

        $this->invokeInaccessibleMethod($gen, 'clearCache', []);
        $this->assertEquals(0, Cache::tags($cacheTag)->get('count'));
        $this->assertFalse(Cache::tags($cacheTag)->has($key));
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
        $cacheTag = 'pingenerator_pins';
        config(['pingenerator.cache_tag' => $cacheTag]);
        $gen = new Generator();

        $res = $this->invokeInaccessibleMethod($gen, 'checkExhausted', []);
        $this->assertFalse($res);

        $validPins = (10 ** $numDigits) - count($obviousNumbers);
        for ($idx = 0; $idx < $validPins; $idx++) {
            $gen->generatePin();
        }

        $this->assertEquals($validPins, Cache::tags($cacheTag)->get('count'));

        $res = $this->invokeInaccessibleMethod($gen, 'checkExhausted', []);
        $this->assertTrue($res);
    }
}

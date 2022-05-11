<?php

namespace Faaizz\PinGenerator;

use Exception;
use Illuminate\Support\Facades\Cache;

class Generator
{
    private int $numDigits;
    private string $fmtStr;
    private string $cacheTag;
    private array $obviousNumbers;

    public function __construct()
    {
        $this->numDigits = config('pingenerator.digits', 4);
        $this->obviousNumbers = config('pingenerator.obvious_numbers', []);
        $this->cacheTag = config('pingenerator.cache_tag', 'pingenerator_pins');
        $this->fmtStr = '%0' . $this->numDigits . 'd';

        // Initialize PIN count in cache
        Cache::tags($this->cacheTag)->put('count', 0);
    }

    // Get random number
    protected function randomNum(): int
    {
        $lowLim = 0;
        $upLim = pow(10, $this->numDigits) - 1;

        return random_int($lowLim, $upLim);
    }

    // Check obvious number
    protected function checkObvious($pin): bool
    {
        return in_array($pin, $this->obviousNumbers);
    }

    // Format pin into the appropriate number of digits by padding with zeros when necessary
    protected function format(int $pin): string
    {
        return sprintf($this->fmtStr, $pin);
    }

    // Check if PIN is already in cache
    protected function checkInCache(int $pin): bool
    {
        $key = $this->cacheTag . $pin;
        return Cache::tags($this->cacheTag)->has($key);
    }

    // Put PIN in cache and increment count
    protected function putInCacheAndIncrementCount(int $pin): void
    {
        $key = $this->cacheTag . $pin;
        Cache::tags($this->cacheTag)->put($key, $pin);
        Cache::tags($this->cacheTag)->increment('count');
    }

    // Clear PINs from cache if all possible PINs have been exhausted
    protected function clearCache(): void
    {
        Cache::tags($this->cacheTag)->flush();
    }

    // Check if all possible PINs have been exhausted
    protected function checkExhausted(): bool
    {
        $possiblePins = 10 ** $this->numDigits;
        $validPins = $possiblePins - count($this->obviousNumbers);

        $count = Cache::tags($this->cacheTag)->get('count');

        return $count >= $validPins;
    }

    // Generate PIN
    public function generatePin(): string
    {
        if ($this->checkExhausted()) {
            $this->clearCache();
        }

        $pin = $this->randomNum();

        $isObvious = $this->checkObvious($pin);
        $obviousSafeguardCtr = 0;
        while ($isObvious || $this->checkInCache($pin)) {
            $pin = $this->randomNum();

            if ($isObvious) {
                $obviousSafeguardCtr++;
            }

            if ($obviousSafeguardCtr >= 100) {
                throw new Exception('unexpected error. 100 obvious numbers generated in sequence.');
            }

            $isObvious = $this->checkObvious($pin);
        }

        $this->putInCacheAndIncrementCount($pin);

        return $this->format($pin);
    }
}

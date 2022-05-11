<?php

namespace Faaizz\PinGenerator;

use Exception;
use Faaizz\PinGenerator\Models\Pin;
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
        sort($this->obviousNumbers);
        $this->fmtStr = '%0' . $this->numDigits . 'd';
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

    // Clear PINs from DB if all possible PINs have been exhausted
    protected function clearPins(): void
    {
        Pin::truncate();
    }

    // Check if all possible PINs have been exhausted
    protected function checkExhausted(): bool
    {
        $possiblePins = 10 ** $this->numDigits;
        $validPins = $possiblePins - count($this->obviousNumbers);

        $count = Pin::all()->count();

        return $count >= $validPins;
    }

    // Check if PIN has already been generated
    protected function checkExists(int $pinNum): bool
    {
        return Pin::where('pin', $pinNum)->count() > 0;
    }

    // Count obvious preceding PINs
    protected function countObviousPrecedingPins(int $pinNum): int
    {
        $obviousPrecedingPinsArr = array_filter($this->obviousNumbers, function ($num) use ($pinNum) {
            return $num < $pinNum;
        });

        return count($obviousPrecedingPinsArr);
    }

    // Check if all preceding valid PINs have been emitted
    protected function checkAllPrecedingPinsEmitted(int $pinNum): bool
    {
        $obviousPrecedingPins = $this->countObviousPrecedingPins($pinNum);
        $validPrecedingPins = $pinNum - $obviousPrecedingPins;

        $emittedPrecedingPins = Pin::where('pin', '<', $pinNum)->count();

        return $validPrecedingPins === $emittedPrecedingPins;
    }

    // Generate PIN
    public function generatePin(): string
    {
        if ($this->checkExhausted()) {
            $this->clearPins();
        }

        $pinNum = $this->randomNum();

        $isObvious = $this->checkObvious($pinNum);
        $obviousSafeguardCtr = 0;
        $exists = $this->checkExists($pinNum);
        $allPrecedingEmitted = $this->checkAllPrecedingPinsEmitted($pinNum);

        while ($isObvious || ($exists && !$allPrecedingEmitted)) {
            $pinNum = $this->randomNum();

            if ($isObvious) {
                $obviousSafeguardCtr++;
            }

            if ($obviousSafeguardCtr >= 100) {
                throw new Exception('unexpected error. 100 obvious numbers generated in sequence.');
            }

            $isObvious = $this->checkObvious($pinNum);

            $exists = $this->checkExists($pinNum);
            $allPrecedingEmitted = $this->checkAllPrecedingPinsEmitted($pinNum);
        }

        if (!$exists) {
            $pin = new Pin();
            $pin->pin = $pinNum;
            $pin->save();
        }

        return $this->format($pinNum);
    }
}

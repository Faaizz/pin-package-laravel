<?php

namespace Faaizz\PinGenerator;

class Generator
{
    private int $numDigits;
    private string $fmtStr;

    public function __construct()
    {
        $this->numDigits = 4;
        $this->fmtStr = '%0' . $this->numDigits . 'd';
    }

    // Generate pin
    public function generate(): int
    {
        $lowLim = 0;
        $upLim = pow(10, $this->numDigits) - 1;

        return random_int($lowLim, $upLim);
    }

    // Format pin into the appropriate number of digits by padding with zeros when necessary
    public function format(int $pin): string
    {
        return sprintf($this->fmtStr, $pin);
    }
}

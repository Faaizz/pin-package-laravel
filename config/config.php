<?php

return [
    // number of digits in generated PINs
    'digits' => env('PIN_DIGITS', 4),
    // "obvious" numbers
    'obvious_numbers' => [
        0000, 1111, 2222, 3333, 4444, 5555, 6666, 7777,
        8888, 9999, 1234
    ],
];

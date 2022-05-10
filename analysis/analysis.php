<?php

use Faaizz\PinGenerator\Generator;

require __DIR__ . '/../vendor/autoload.php';

function config(string $key)
{
    switch ($key) {
        case 'pingenerator.digits':
            return 4;
            break;
        case 'pingenerator.obvious_numbers':
            return [];
    }
}

function generatePin(): string
{
    $gen = new Generator();
    return $gen->generatePin();
}

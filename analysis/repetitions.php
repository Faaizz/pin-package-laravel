<?php

require __DIR__ . '/analysis.php';

function repetition(): int
{
    $pin = generatePin();
    $ctr = 0;

    while (true) {
        $newPin = generatePin();

        if (strcmp($pin, $newPin) === 0) {
            break;
        }
        $ctr++;
    }

    return $ctr;
}

function countRepetitions(int $times): array
{
    $ctrArr = [];

    for ($idx = 0; $idx < $times; $idx++) {
        echo sprintf("%d of %d\n", $idx + 1, $times);
        array_push($ctrArr, repetition());
    }

    return $ctrArr;
}

$ctrArr = countRepetitions(1000);

$outJson = json_encode($ctrArr);
file_put_contents(
    'repetitions.json',
    $outJson,
);

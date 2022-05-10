<?php

require __DIR__ . '/analysis.php';

function repetition(int $times): array
{
    $inArr = [];

    for ($idx = 0; $idx < $times; $idx++) {
        array_push($inArr, generatePin());
    }

    return $inArr;
}

function countVariables(int $xDim, $yDim): array
{
    $matArr = [];

    for ($idx = 0; $idx < $xDim; $idx++) {
        echo sprintf("%d of %d\n", $idx + 1, $xDim);
        array_push($matArr, repetition($yDim));
    }

    return $matArr;
}

$matArr = countVariables(10, 10);

$outJson = json_encode($matArr);
file_put_contents(
    'correlation.json',
    $outJson,
);

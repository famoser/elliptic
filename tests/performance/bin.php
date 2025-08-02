<?php

require __DIR__ . '/../../vendor/autoload.php';

use Famoser\Elliptic\Tests\Performance\Driver;

// usage: php ./bin -i=1000 -r=4
$options = getopt("c::i::", ["collector::iterations::"]);
$iterationPerRound = intval($options["iterations"] ?? $options["i"] ?? 1000);
$maxRounds = intval($options["rounds"] ?? $options["r"] ?? null);

$collector = new Driver();
$collector->collectMeasurements($iterationPerRound, $maxRounds);

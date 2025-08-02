<?php

require __DIR__ . '/../../vendor/autoload.php';

use Famoser\Elliptic\Tests\ConstTime\Driver;

// usage: php ./bin -c=1 -i=1000
$options = getopt("c::i::", ["collector::iterations::"]);
$collectorIndex = intval($options["collector"] ?? $options["c"] ?? 0);
$maxIterations = intval($options["iterations"] ?? $options["i"] ?? null);

$collector = new Driver();
$collector->collectMeasurements($collectorIndex, $maxIterations);

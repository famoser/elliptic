<?php

require __DIR__ . '/../../vendor/autoload.php';

use Famoser\Elliptic\ConstTime\MeasurementCollector;

$options = getopt("c::", ["collector::"]);
$collectorIndex = intval($options["collector"] ?? $options["c"] ?? 0);

$collector = new MeasurementCollector();
$collector->collectMeasurements($collectorIndex);

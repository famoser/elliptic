<?php

require __DIR__ . '/../../vendor/autoload.php';

use Famoser\Elliptic\ConstTime\MeasurementCollector;

// Script example.php
$options = getopt("s::", ["samples::"]);
$sampleCount = intval($options["samples"] ?? $options["s"] ?? 1000);

$collector = new MeasurementCollector();
foreach ($collector->createMeasurementTargets() as $measurementTarget) {
    $collector->collectMeasurements($measurementTarget, $sampleCount);
}

<?php

require __DIR__ . '/../../vendor/autoload.php';

use Famoser\Elliptic\ConstTime\MeasurementCollector;

$collector = new MeasurementCollector();
$collector->collectMeasurements();

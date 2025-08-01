<?php

namespace Famoser\Elliptic\ConstTime;

use Famoser\Elliptic\ConstTime\Collectors\EcdsaSha256WycheProofCollector;
use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\SWUnsafeMath;

class MeasurementCollector
{
    public function collectMeasurements(): void
    {
        $curveRepository = new CurveRepository();
        $curveName = 'secp192r1';
        $math = new SWUnsafeMath($curveRepository->findByName($curveName));

        $proofCollector = new EcdsaSha256WycheProofCollector($curveName, $math);
        $iteration = 0;
        while (true) {
            $proofCollector->collect();
            $iteration++;
            echo "Iteration: ".$iteration."\n";

            if ($iteration % 100 === 0) {
                $output = $proofCollector->store();
                $this->storeMeasurement($curveName, $math, $iteration, $output);

                echo "Stored.\n";
            }
        }
    }

    private function storeMeasurement(string $curveName, MathInterface $math, int $sampleCount, array $payload): void
    {
        $mathName = substr($math::class, strrpos($math::class, '\\') + 1);
        $filename = $mathName . "_" . $curveName . "_" . $sampleCount . ".json";
        $path = __DIR__ . DIRECTORY_SEPARATOR . "measurements" . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($path, json_encode($payload));
    }
}

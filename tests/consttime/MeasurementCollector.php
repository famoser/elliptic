<?php

namespace Famoser\Elliptic\ConstTime;

use Famoser\Elliptic\ConstTime\Collectors\EcdsaSha256ProofCollector;
use Famoser\Elliptic\ConstTime\Collectors\ProofCollectorInterface;
use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\SW_QT_ANeg3_Math;
use Famoser\Elliptic\Math\SWUnsafeMath;

class MeasurementCollector
{
    /**
     * @return ProofCollectorInterface[]
     */
    private function proofCollectors(): array
    {
        $repo = new CurveRepository();

        $brainpoolP192r1Math = new SW_QT_ANeg3_Math($repo->findByName('brainpoolP192r1'), BrainpoolCurveFactory::p192r1TwistToP192t1());
        return [
            EcdsaSha256ProofCollector::createFromWyche('secp192r1', new SWUnsafeMath($repo->findByName('secp192r1'))),
            EcdsaSha256ProofCollector::createFromWyche('secp192r1', new SW_ANeg3_Math($repo->findByName('secp192r1'))),
            EcdsaSha256ProofCollector::createFromRooterberg('brainpool_p192r1', 224, $brainpoolP192r1Math),
        ];
    }

    public function collectMeasurements(int $collectorIndex): void
    {
        $proofCollector = $this->proofCollectors()[$collectorIndex];

        echo "Starting measurement of collector with index " . $collectorIndex . ":\n";
        echo $proofCollector->getMath()::class . " using " . $proofCollector->getCurveName() . "\n\n";

        $iteration = 0;
        while (true) {
            $proofCollector->collect();
            $iteration++;
            echo "Iteration: " . $iteration . "\n";

            if ($iteration % 100 === 0) {
                $output = $proofCollector->store();
                $this->storeMeasurement($proofCollector->getMath(), $proofCollector->getCurveName(), $iteration, $output);

                echo "Stored.\n";
            }
        }
    }

    private function storeMeasurement(MathInterface $math, string $curveName, int $iterationCount, array $payload): void
    {
        $mathName = substr($math::class, strrpos($math::class, '\\') + 1);
        $filename = $mathName . "_" . $curveName . "_" . $iterationCount . ".json";
        $path = __DIR__ . DIRECTORY_SEPARATOR . "measurements" . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($path, json_encode($payload));
    }
}

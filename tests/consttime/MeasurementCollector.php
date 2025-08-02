<?php

namespace Famoser\Elliptic\ConstTime;

use Famoser\Elliptic\ConstTime\Collectors\EcdsaShaProofCollector;
use Famoser\Elliptic\ConstTime\Collectors\EddsaProofCollector;
use Famoser\Elliptic\ConstTime\Collectors\ProofCollectorInterface;
use Famoser\Elliptic\ConstTime\Collectors\XdhCalculatorProofCollector;
use Famoser\Elliptic\ConstTime\Collectors\XdhMathProofCollector;
use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Math\Calculator\MGXCalculator;
use Famoser\Elliptic\Math\EDMath;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\MG_TwED_ANeg1_Math;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\SW_QT_ANeg3_Math;
use Famoser\Elliptic\Math\SWUnsafeMath;
use Famoser\Elliptic\Math\TwED_ANeg1_Math;

class MeasurementCollector
{
    /**
     * @return ProofCollectorInterface[]
     */
    private function proofCollectors(): array
    {
        $repo = new CurveRepository();

        $brainpoolP192r1Math = new SW_QT_ANeg3_Math($repo->findByName('brainpoolP192r1'), BrainpoolCurveFactory::p192r1TwistToP192t1());
        $curve25519Math = new MG_TwED_ANeg1_Math($repo->findByName('curve25519'), BernsteinCurveFactory::curve25519ToEdwards25519(), $repo->findByName('edwards25519'));
        return [
            EcdsaShaProofCollector::createFromWycheSha256('secp192r1', new SWUnsafeMath($repo->findByName('secp192r1'))),
            EcdsaShaProofCollector::createFromWycheSha256('secp192r1', new SW_ANeg3_Math($repo->findByName('secp192r1'))),
            EcdsaShaProofCollector::createFromRooterberg('brainpool_p192r1', 224, $brainpoolP192r1Math),
            XdhCalculatorProofCollector::createForCurve25519(new MGXCalculator(BernsteinCurveFactory::curve25519())),
            XdhMathProofCollector::createForCurve25519($curve25519Math),
            EddsaProofCollector::createEd25519(new TwED_ANeg1_Math($repo->findByName('edwards25519'))),
            EddsaProofCollector::createEd448(new EDMath($repo->findByName('edwards448'))),
        ];
    }

    public function collectMeasurements(int $collectorIndex): void
    {
        $proofCollector = $this->proofCollectors()[$collectorIndex];

        echo "Starting measurement of collector with index " . $collectorIndex . ":\n";
        echo $proofCollector->getMathName() . " using " . $proofCollector->getCurveName() . "\n\n";

        $iteration = 0;
        while (true) {
            $proofCollector->collect();
            $iteration++;
            echo "Iteration: " . $iteration . "\n";

            if ($iteration % 100 === 0) {
                $output = $proofCollector->store();
                $this->storeMeasurement($proofCollector->getMathName(), $proofCollector->getCurveName(), $iteration, $output);

                echo "Stored.\n";
            }
        }
    }

    private function storeMeasurement(string $mathName, string $curveName, int $iterationCount, array $payload): void
    {
        $filename = $mathName . "_" . $curveName . "_" . $iterationCount . ".json";
        $path = __DIR__ . DIRECTORY_SEPARATOR . "measurements" . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($path, json_encode($payload));
    }
}

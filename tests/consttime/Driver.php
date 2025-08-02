<?php

namespace Famoser\Elliptic\Tests\ConstTime;

use Famoser\Elliptic\Tests\ConstTime\Collectors\EcdsaShaCollector;
use Famoser\Elliptic\Tests\ConstTime\Collectors\EddsaCollector;
use Famoser\Elliptic\Tests\ConstTime\Collectors\CollectorInterface;
use Famoser\Elliptic\Tests\ConstTime\Collectors\XdhCalculatorCollector;
use Famoser\Elliptic\Tests\ConstTime\Collectors\XdhMathCollector;
use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Math\Calculator\MGXCalculator;
use Famoser\Elliptic\Math\EDMath;
use Famoser\Elliptic\Math\EDUnsafeMath;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\MG_TwED_ANeg1_Math;
use Famoser\Elliptic\Math\MGUnsafeMath;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\SW_QT_ANeg3_Math;
use Famoser\Elliptic\Math\SWUnsafeMath;
use Famoser\Elliptic\Math\TwED_ANeg1_Math;
use Famoser\Elliptic\Math\TwEDUnsafeMath;

class Driver
{
    private function createCollector(int $index): CollectorInterface
    {
        $repo = new CurveRepository();

        $brainpoolP192r1Math = new SW_QT_ANeg3_Math($repo->findByName('brainpoolP192r1'), BrainpoolCurveFactory::p192r1TwistToP192t1());
        $curve25519Math = new MG_TwED_ANeg1_Math($repo->findByName('curve25519'), BernsteinCurveFactory::curve25519ToEdwards25519(), $repo->findByName('edwards25519'));
        $collectors = [
            // hardened math test
            EcdsaShaCollector::createFromWycheSha256('secp192r1', new SW_ANeg3_Math($repo->findByName('secp192r1'))),
            EcdsaShaCollector::createFromRooterberg('brainpool_p192r1', 224, $brainpoolP192r1Math),
            XdhCalculatorCollector::createForCurve25519(new MGXCalculator(BernsteinCurveFactory::curve25519())),
            XdhMathCollector::createForCurve25519($curve25519Math),
            EddsaCollector::createEd25519(new TwED_ANeg1_Math($repo->findByName('edwards25519'))),
            EddsaCollector::createEd448(new EDMath($repo->findByName('edwards448'))),

            // unsafe math test
            EcdsaShaCollector::createFromWycheSha256('secp192r1', new SWUnsafeMath($repo->findByName('secp192r1'))),
            XdhMathCollector::createForCurve25519(new MGUnsafeMath($repo->findByName('curve25519'))),
            EddsaCollector::createEd25519(new TwEDUnsafeMath($repo->findByName('edwards25519'))),
            EddsaCollector::createEd448(new EDUnsafeMath($repo->findByName('edwards448'))),
        ];

        return $collectors[$index];
    }

    public function collectMeasurements(int $collectorIndex, ?int $maxIterations): void
    {
        $proofCollector = $this->createCollector($collectorIndex);

        echo "Starting measurement of collector with index " . $collectorIndex . ":\n";
        echo $proofCollector->getMathName() . " using " . $proofCollector->getCurveName() . "\n\n";

        $iteration = 0;
        while (true) {
            $proofCollector->collect();
            $iteration++;
            echo "Iteration: " . $iteration . "\n";

            $lastIteration = $maxIterations && $maxIterations <= $iteration;
            if ($iteration % 100 === 0 || $lastIteration) {
                $output = $proofCollector->store();
                $this->storeMeasurement($proofCollector->getMathName(), $proofCollector->getCurveName(), $iteration, $output);

                echo "Stored.\n";
            }

            if ($lastIteration) {
                echo "Last iteration reached, stopping.\n";
                break;
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

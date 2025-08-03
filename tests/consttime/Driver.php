<?php

namespace Famoser\Elliptic\Tests\ConstTime;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Math\Calculator\MGXCalculator;
use Famoser\Elliptic\Math\EDMath;
use Famoser\Elliptic\Math\EDUnsafeMath;
use Famoser\Elliptic\Math\MG_TwED_ANeg1_Math;
use Famoser\Elliptic\Math\MGUnsafeMath;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\SW_QT_ANeg3_Math;
use Famoser\Elliptic\Math\SWUnsafeMath;
use Famoser\Elliptic\Math\TwED_ANeg1_Math;
use Famoser\Elliptic\Math\TwEDUnsafeMath;
use Famoser\Elliptic\Tests\ConstTime\Collector\CollectorInterface;
use Famoser\Elliptic\Tests\ConstTime\CollectorFactory\EcdsaShaCollectorFactory;
use Famoser\Elliptic\Tests\ConstTime\CollectorFactory\EddsaCollectorFactory;
use Famoser\Elliptic\Tests\ConstTime\CollectorFactory\XdhCollectorFactory;

class Driver
{
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

    private function createCollector(int $index): CollectorInterface
    {
        $repo = new CurveRepository();

        $brainpoolP192r1Math = new SW_QT_ANeg3_Math($repo->findByName('brainpoolP192r1'), BrainpoolCurveFactory::p192r1TwistToP192t1());
        $curve25519Math = new MG_TwED_ANeg1_Math($repo->findByName('curve25519'), BernsteinCurveFactory::curve25519ToEdwards25519(), $repo->findByName('edwards25519'));
        $collectors = [
            // hardened math test
            fn () => EcdsaShaCollectorFactory::createFromWycheSha256('secp192r1', new SW_ANeg3_Math($repo->findByName('secp192r1'))),
            fn () => EcdsaShaCollectorFactory::createFromRooterberg('brainpool_p192r1', $brainpoolP192r1Math, 224),
            fn () => XdhCollectorFactory::createForCurve25519Calculator(new MGXCalculator(BernsteinCurveFactory::curve25519())),
            fn () => XdhCollectorFactory::createForCurve25519Math($curve25519Math),
            fn () => EddsaCollectorFactory::createEd25519(new TwED_ANeg1_Math($repo->findByName('edwards25519'))),
            fn () => EddsaCollectorFactory::createEd448(new EDMath($repo->findByName('edwards448'))),

            // unsafe math test
            fn () => EcdsaShaCollectorFactory::createFromWycheSha256('secp192r1', new SWUnsafeMath($repo->findByName('secp192r1'))),
            fn () => XdhCollectorFactory::createForCurve25519Math(new MGUnsafeMath($repo->findByName('curve25519'))),
            fn () => EddsaCollectorFactory::createEd25519(new TwEDUnsafeMath($repo->findByName('edwards25519'))),
            fn () => EddsaCollectorFactory::createEd448(new EDUnsafeMath($repo->findByName('edwards448'))),

            // comparison with phpecc
            fn () => EcdsaShaCollectorFactory::createPhpeccBrainpool256r1($repo->findByName('brainpoolP256r1')),
        ];

        return $collectors[$index]();
    }

    private function storeMeasurement(string $mathName, string $curveName, int $iterationCount, array $payload): void
    {
        $filename = $mathName . "_" . $curveName . "_" . $iterationCount . ".json";
        $path = __DIR__ . DIRECTORY_SEPARATOR . "measurements" . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($path, json_encode($payload));
    }
}

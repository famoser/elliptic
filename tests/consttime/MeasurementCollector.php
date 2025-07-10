<?php

namespace Famoser\Elliptic\ConstTime;

use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\SW_QT_ANeg3_Math;
use Famoser\Elliptic\Math\SWUnsafeMath;

class MeasurementCollector
{
    /**
     * @return MathInterface[]
     */
    public function createMeasurementTargets(): array
    {
        $secp521r1 = self::getRepository()->findByName('secp521r1');
        $p512r1 = self::getRepository()->findByName('brainpoolP512r1');
        $p512r1Twist = BrainpoolCurveFactory::p512r1TwistToP512t1();

        return [
            new SWUnsafeMath($secp521r1),
            new SW_ANeg3_Math($secp521r1),
            new SW_QT_ANeg3_Math($p512r1, $p512r1Twist),
        ];
    }

    public function collectMeasurements(MathInterface $math, int $sampleCount): void
    {
        [$baseline, $measurement] = $this->measureBaselineAgainstRandomPoint($math, $sampleCount);
        $payload = ["baseline" => $baseline, "measurement" => $measurement];
        $this->storeMeasurement("baseline_random", $math, $sampleCount, $payload);
        ;
    }

    private static ?CurveRepository $curveRepository = null;

    private static function getRepository(): CurveRepository
    {
        if (self::$curveRepository === null) {
            self::$curveRepository = new CurveRepository();
        }

        return self::$curveRepository;
    }

    private function measureBaselineAgainstRandomPoint(MathInterface $math, int $sampleCount): array
    {
        $fixedFactor = gmp_random_range(0, $math->getCurve()->getN());
        $randomFactors = [];
        for ($i = 0; $i < $sampleCount; $i++) {
            $randomFactors[] = gmp_random_range(0, $math->getCurve()->getN());
        }

        $baseline = [];
        $measurement = [];
        for ($i = 0; $i < $sampleCount; $i++) {
            $start = microtime(true);
            $math->mul($math->getCurve()->getG(), $fixedFactor);
            $end = microtime(true);
            $baseline[] = $end - $start;

            $variableFactor = $randomFactors[$i];
            $start = microtime(true);
            $math->mul($math->getCurve()->getG(), $variableFactor);
            $end = microtime(true);
            $measurement[] = $end - $start;
        }

        return [$baseline, $measurement];
    }

    private function storeMeasurement(string $measurementType, MathInterface $math, int $sampleCount, array $payload): void
    {
        $mathName = substr($math::class, strrpos($math::class, '\\') + 1);
        $curveName = self::getRepository()->getCanonicalName($math->getCurve());
        $filename = $measurementType . "_" . $mathName . "_" . $curveName . "_" . $sampleCount . ".json";
        $path = __DIR__ . DIRECTORY_SEPARATOR . "measurements" . DIRECTORY_SEPARATOR . $filename;

        $payload = [
            "math" => $mathName,
            "curve" => $curveName,
            ...$payload
        ];

        file_put_contents($path, json_encode($payload));
    }
}

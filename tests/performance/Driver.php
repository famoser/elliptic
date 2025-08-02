<?php

namespace Famoser\Elliptic\Tests\Performance;

use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Math\MathFactory;
use Famoser\Elliptic\Tests\Performance\Collectors\CollectorInterface;
use Famoser\Elliptic\Tests\Performance\Collectors\MathMulGCollector;

class Driver
{
    public function collectMeasurements(int $iterationsPerRound, ?int $maxRounds): void
    {
        $collectors = $this->createCollectors();
        echo "Starting performance measurement of " . count($collectors) . " targets.\n";

        $round = 0;
        while (true) {
            foreach ($collectors as $index => $target) {
                $target->collect($iterationsPerRound);
                $this->storeMeasurement($target->getMathName(), $target->getCurveName(), $target->getCollectorId(), $iterationsPerRound * ($round + 1), $target->store());
                echo "Finished round for target " . ($index+1) . "/" . count($collectors) . ".\n";
            }

            if ($maxRounds && $round <= $maxRounds) {
                echo "Last round reached, stopping.\n";
                break;
            }
        }
    }

    /**
     * @return CollectorInterface[]
     */
    private function createCollectors(): array
    {
        $curveRepository = new CurveRepository();
        $mathFactory = new MathFactory($curveRepository);
        $measurementTargets = [];
        foreach ($curveRepository->getCanonicalNames() as $canonicalName) {
            $curve = $curveRepository->findByName($canonicalName);

            $unsafeMath = $mathFactory->createUnsafeMath($curve);
            $measurementTargets[] = new MathMulGCollector($canonicalName, $curve, $unsafeMath);

            $hardenedMath = $mathFactory->createHardenedMath($curve);
            if ($hardenedMath !== null) {
                $measurementTargets[] = new MathMulGCollector($canonicalName, $curve, $hardenedMath);
            }
        }

        return $measurementTargets;
    }

    private function storeMeasurement(string $mathName, string $curveName, string $collectorId, int $iterationCount, array $payload): void
    {
        $filename = $mathName . "_" . $curveName . "_" . $collectorId . "_" . $iterationCount . ".json";
        $path = __DIR__ . DIRECTORY_SEPARATOR . "measurements" . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($path, json_encode($payload));
    }
}

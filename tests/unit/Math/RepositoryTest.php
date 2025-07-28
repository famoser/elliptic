<?php

namespace Famoser\Elliptic\Tests\Math;

use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Math\AbstractMath;
use Famoser\Elliptic\Math\MathFactory;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    public function testAllCurvesHaveUnsafeMath(): void
    {
        $curveRepository = new CurveRepository();
        $mathFactory = new MathFactory($curveRepository);

        foreach ($curveRepository->getKnownNames() as $knownName) {
            $curve = $curveRepository->findByName($knownName);
            $math = $mathFactory->createUnsafeMath($curve);
            $this->assertInstanceOf(AbstractMath::class, $math);
        }
    }

    public function testExpectedCurvesHaveHardenedMath(): void
    {
        $curveRepository = new CurveRepository();
        $mathFactory = new MathFactory($curveRepository);

        foreach ($curveRepository->getKnownNames() as $knownName) {
            $curve = $curveRepository->findByName($knownName);
            $curveName = $curveRepository->getCanonicalName($curve);

            if (str_starts_with($curveName, 'secp') && str_ends_with($curveName, 'k1')) {
                continue;
            }

            if (str_starts_with($curveName, 'brainpool') && str_ends_with($curveName, 'k1')) {
                continue;
            }

            $math = $mathFactory->createHardenedMath($curve);
            $this->assertInstanceOf(AbstractMath::class, $math, 'math for curve ' . $curveName . ' is missing');;
        }
    }
}

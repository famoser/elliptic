<?php

namespace Famoser\Elliptic\Tests\Unit\Math;

use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Math\AbstractMath;
use Famoser\Elliptic\Math\MathFactory;
use Famoser\Elliptic\Primitives\Curve;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    public static function provideCurves(): array
    {
        $curveRepository = new CurveRepository();
        $testSets = [];
        foreach ($curveRepository->getKnownNames() as $knownName) {
            $curve = $curveRepository->findByName($knownName);
            $canonicalName = $curveRepository->getCanonicalName($curve);

            $testSets[] = [$curveRepository, $curve, $canonicalName];
        }

        return $testSets;
    }

    /**
     * @dataProvider provideCurves
     */
    public function testAllCurvesHaveUnsafeMath(CurveRepository $curveRepository, Curve $curve): void
    {
        $mathFactory = new MathFactory($curveRepository);

        $math = $mathFactory->createUnsafeMath($curve);
        $this->assertInstanceOf(AbstractMath::class, $math);
    }

    /**
     * @dataProvider provideCurves
     */
    public function testExpectedCurvesHaveHardenedMath(CurveRepository $curveRepository, Curve $curve, string $canonicalName): void
    {
        $mathFactory = new MathFactory($curveRepository);
        $math = $mathFactory->createHardenedMath($curve);

        if ($this->hasHardenedMath($canonicalName)) {
            $this->assertInstanceOf(AbstractMath::class, $math, 'hardened math for curve ' . $canonicalName . ' is missing.');
        } else {
            $this->assertNull($math, 'hardened math for curve ' . $canonicalName . ' is exists.');
        }
    }

    private function hasHardenedMath(string $curveName): bool
    {
        return !(
            str_starts_with($curveName, 'secp') && str_ends_with($curveName, 'k1') ||
            str_starts_with($curveName, 'brainpool') && str_ends_with($curveName, 'k1')
        );
    }
}

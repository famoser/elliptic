<?php

namespace Famoser\Elliptic\Tests\Curves;

use Famoser\Elliptic\Curves\CurveRepository;
use PHPUnit\Framework\TestCase;

class CurveRepositoryTest extends TestCase
{
    public function testAllKnownNamesReturnCurves(): void
    {
        $curveRepository = new CurveRepository();

        foreach ($curveRepository->getKnownNames() as $knownName) {
            $this->assertNotNull($curveRepository->findByName($knownName));
        }
    }

    public function testAllKnownOIDsReturnCurves(): void
    {
        $curveRepository = new CurveRepository();

        foreach ($curveRepository->getKnownCurveOIDs() as $knownCurveOID) {
            $this->assertNotNull($curveRepository->findByOID($knownCurveOID));
        }
    }

    public function testNonSenseReturnsNull(): void
    {
        $curveRepository = new CurveRepository();
        $this->assertNull($curveRepository->findByName('unknown'));
        $this->assertNull($curveRepository->findByOID('1.3.132.0.99'));
    }

    public function testSingleInstanceOfCurves(): void
    {
        $curveRepository = new CurveRepository();

        $someCurveOID = $curveRepository->getKnownCurveOIDs()[0];
        $curve1 = $curveRepository->findByOID($someCurveOID);
        $curve2 = $curveRepository->findByOID($someCurveOID);

        $this->assertEquals($curve1, $curve2);
    }
}

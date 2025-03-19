<?php

namespace Famoser\Elliptic\Tests\Curves;

use Famoser\Elliptic\Curves\CurveRepository;
use PHPUnit\Framework\TestCase;

class CurveRepositoryTest extends TestCase
{
    public function testAllKnownNamesReturnCurves()
    {
        $curveRepository = new CurveRepository();

        foreach ($curveRepository->getKnownCurveOIDs() as $knownCurveOID) {
            $this->assertNotNull($curveRepository->findByOID($knownCurveOID));
        }
    }

    public function testAllKnownOIDsReturnCurves()
    {
        $curveRepository = new CurveRepository();

        foreach ($curveRepository->getKnownCurveOIDs() as $knownCurveOID) {
            $this->assertNotNull($curveRepository->findByOID($knownCurveOID));
        }
    }

    public function testSingleInstanceOfCurves()
    {
        $curveRepository = new CurveRepository();

        $someCurveOID = $curveRepository->getKnownCurveOIDs()[0];
        $curve1 = $curveRepository->findByOID($someCurveOID);
        $curve2 = $curveRepository->findByOID($someCurveOID);

        $this->assertEquals($curve1, $curve2);
    }
}

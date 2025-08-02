<?php

namespace Famoser\Elliptic\Tests\Unit\Curves;

use Famoser\Elliptic\Curves\CurveRepository;
use PHPUnit\Framework\TestCase;

class CurveRepositoryTest extends TestCase
{
    public function testAllCanonicalNamesReturnCurves(): void
    {
        $curveRepository = new CurveRepository();

        foreach ($curveRepository->getCanonicalNames() as $knownName) {
            $this->assertNotNull($curveRepository->findByName($knownName));
        }
    }

    public function testAllNamesReturnCurves(): void
    {
        $curveRepository = new CurveRepository();

        foreach ($curveRepository->getNames() as $knownName) {
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

    public function testCanonicalNameReturned(): void
    {
        $curveRepository = new CurveRepository();
        $name = 'secp256k1';
        $curve = $curveRepository->findByName($name);
        $this->assertEquals($name, $curveRepository->getCanonicalName($curve));
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

        $this->assertSame($curve1, $curve2);
    }
}

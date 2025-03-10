<?php

namespace Mdanter\Ecc\Integration\Spec;

use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Curves\NamedCurveFp;
use PHPUnit\Framework\TestCase;

class PublicKeyTest extends TestCase
{
    public function getPublicKeyFixtures(): array
    {
        $files = FixturesRepository::read('pubkey');
        $datasets = [];

        foreach ($files as $file) {
            $curve = CurveFactory::getCurveByName($file['curve']);
            foreach ($file['fixtures'] as $i => $fixture) {
                $datasetIdentifier = $file['file'] . "." . $i;

                $datasets[$datasetIdentifier] = [
                    $curve,
                    gmp_init($fixture['x'], 16),
                    gmp_init($fixture['y'], 16),
                    $fixture['result'],
                ];
            }
        }

        return $datasets;
    }

    /**
     * @dataProvider getPublicKeyFixtures
     */
    public function testPublicKeyFrom(NamedCurveFp $curve, \GMP $x, \GMP $y, bool $expectedResult)
    {
        $generator = CurveFactory::getGeneratorByName($curve->getName());
        try {
            $generator->getPublicKeyFrom($x, $y);
            $pointOnCurve = true;
        } catch (\Exception $e) {
            $pointOnCurve = false;
        }

        $this->assertEquals($expectedResult, $pointOnCurve);
    }

    /**
     * @dataProvider getPublicKeyFixtures
     */
    public function testContains(NamedCurveFp $curve, \GMP $x, \GMP $y, bool $expectedResult)
    {
        // dataset is relative to public keys, and there are less public keys valid than points on curve
        if (!$expectedResult) {
            $this->markTestSkipped();
        }

        $result = $curve->contains($x, $y);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider getPublicKeyFixtures
     */
    public function testGetPoint(NamedCurveFp $curve, \GMP $x, \GMP $y, bool $expectedResult)
    {
        // dataset is relative to public keys, and there are less public keys valid than points on curve
        if (!$expectedResult) {
            $this->markTestSkipped();
        }

        try {
            $curve->getPoint($x, $y);
            $pointOnCurve = true;
        } catch (\Exception $e) {
            $pointOnCurve = false;
        }

        $this->assertTrue($pointOnCurve);
    }
}

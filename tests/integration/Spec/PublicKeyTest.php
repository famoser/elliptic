<?php

namespace Mdanter\Ecc\Integration\Spec;

use Mdanter\Ecc\Curves\CurveRepository;
use Mdanter\Ecc\Legacy\Curves\CurveFactory;
use Mdanter\Ecc\Legacy\Curves\NamedCurveFp;
use Mdanter\Ecc\Primitives\Curve;
use Mdanter\Ecc\Serializer\PointDecoder;
use PHPUnit\Framework\TestCase;

class PublicKeyTest extends TestCase
{
    public static function getPublicKeyFixtures(): array
    {
        $curveRepository = new CurveRepository();

        $files = FixturesRepository::read('pubkey');
        $datasets = [];

        foreach ($files as $file) {
            $curve = $curveRepository->findByName($file['curve']);
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
    public function testPublicKeyFrom(Curve $curve, \GMP $x, \GMP $y, bool $expectedResult)
    {
        $pointDecoder = new PointDecoder($curve);
        try {
            $point = $pointDecoder->fromCoordinates($x, $y);
            $pointOnCurve = true;
        } catch (\Exception) {
            $pointOnCurve = false;
        }

        $this->assertEquals($expectedResult, $pointOnCurve);
    }
}

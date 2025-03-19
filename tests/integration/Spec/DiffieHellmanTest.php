<?php

namespace Mdanter\Ecc\Integration\Spec;

use Mdanter\Ecc\Curves\CurveRepository;
use Mdanter\Ecc\Math\UnsafeMath;
use Mdanter\Ecc\Primitives\Curve;
use PHPUnit\Framework\TestCase;

class DiffieHellmanTest extends TestCase
{
    /**
     * @return array
     */
    public function getDiffieHellmanFixtures(): array
    {
        $curveRepository = new CurveRepository();

        $files = FixturesRepository::read('diffie');
        $datasets = [];

        foreach ($files as $file) {
            $curve = $curveRepository->findByName($file['curve']);

            foreach ($file['fixtures'] as $i => $fixture) {
                $datasetIdentifier = $file['file'] . "." . $i;

                $datasets[$datasetIdentifier] = [
                    $curve,
                    gmp_init($fixture['alice'], 10),
                    gmp_init($fixture['bob'], 10),
                    gmp_init($fixture['shared'], 16)
                ];
            }
        }

        return $datasets;
    }

    /**
     * @dataProvider getDiffieHellmanFixtures()
     */
    public function testDiffieHellman(Curve $curve, \GMP $alice, \GMP $bob, \GMP $expectedX)
    {
        $math = new UnsafeMath($curve);
        $Ga = $math->mul($curve->getG(), $alice);
        $Gb = $math->mul($curve->getG(), $bob);

        $Gab = $math->mul($Ga, $bob);
        $Gba = $math->mul($Gb, $alice);

        $this->assertEquals($expectedX, $Gab->x);
        $this->assertEquals($expectedX, $Gba->x);
    }
}

<?php

namespace Mdanter\Ecc\Integration\Spec;

use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use PHPUnit\Framework\TestCase;

class DiffieHellmanTest extends TestCase
{
    /**
     * @return array
     */
    public function getDiffieHellmanFixtures(): array
    {
        $files = FixturesRepository::read('diffie');
        $datasets = [];

        foreach ($files as $file) {
            $generator = CurveFactory::getGeneratorByName($file['curve']);

            foreach ($file['fixtures'] as $i => $fixture) {
                $datasetIdentifier = $file['file'] . "." . $i;

                $datasets[$datasetIdentifier] = [
                    $generator,
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
    public function testDiffieHellman(GeneratorPoint $generator, \GMP $alice, \GMP $bob, \GMP $expectedX)
    {
        $alicePrivKey = $generator->getPrivateKeyFrom($alice);
        $bobPrivKey = $generator->getPrivateKeyFrom($bob);

        $aliceDh = $alicePrivKey->createExchange($bobPrivKey->getPublicKey());
        $bobDh = $bobPrivKey->createExchange($alicePrivKey->getPublicKey());

        $this->assertEquals($expectedX, $aliceDh->calculateSharedKey());
        $this->assertEquals($expectedX, $bobDh->calculateSharedKey());
    }
}

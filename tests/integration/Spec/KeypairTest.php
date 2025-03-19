<?php

namespace Mdanter\Ecc\Integration\Spec;

use Mdanter\Ecc\Legacy\Curves\CurveFactory;
use Mdanter\Ecc\Legacy\Primitives\GeneratorPoint;
use Mdanter\Ecc\Legacy\Serializer\Point\Format\CompressedPointSerializer;
use Mdanter\Ecc\Legacy\Serializer\Point\Format\UncompressedPointSerializer;
use PHPUnit\Framework\TestCase;

class KeypairTest extends TestCase
{
    /**
     * @return array
     */
    public function getKeypairFixtures(): array
    {
        $files = FixturesRepository::read('keypairs');
        $datasets = [];

        foreach ($files as $file) {
            $generator = CurveFactory::getGeneratorByName($file['curve']);
            foreach ($file['fixtures'] as $i => $fixture) {
                $datasetIdentifier = $file['file'] . "." . $i;

                $datasets[$datasetIdentifier] = [
                    $generator,
                    gmp_init((string)$fixture['k'], 10),
                    gmp_init($fixture['x'], 16),
                    gmp_init($fixture['y'], 16)
                ];
            }
        }

        return $datasets;
    }

    /**
     * @dataProvider getKeypairFixtures()
     */
    public function testGetPublicKey(GeneratorPoint $generator, \GMP $k, \GMP $expectedX, \GMP $expectedY)
    {
        $privateKey = $generator->getPrivateKeyFrom($k);
        $publicKey = $privateKey->getPublicKey();

        $this->assertEquals($expectedX, $publicKey->getPoint()->getX());
        $this->assertEquals($expectedY, $publicKey->getPoint()->getY());
    }

    /**
     * @dataProvider getKeypairFixtures()
     */
    public function testPointSerializers(GeneratorPoint $generator, \GMP $k, \GMP $expectedX, \GMP $expectedY)
    {
        $adapter = $generator->getAdapter();

        $publicKey = $generator->getPublicKeyFrom($expectedX, $expectedY);

        $serializer = new UncompressedPointSerializer();
        $serialized = $serializer->serialize($publicKey->getPoint());
        $parsed = $serializer->deserialize($generator->getCurve(), $serialized);
        $this->assertTrue($parsed->equals($publicKey->getPoint()));

        $compressingSerializer = new CompressedPointSerializer($adapter);
        $serialized = $compressingSerializer->serialize($publicKey->getPoint());
        $parsed = $compressingSerializer->deserialize($generator->getCurve(), $serialized);
        $this->assertTrue($parsed->equals($publicKey->getPoint()));
    }
}

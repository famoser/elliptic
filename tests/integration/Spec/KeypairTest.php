<?php

namespace Mdanter\Ecc\Integration\Spec;

use Mdanter\Ecc\Curves\CurveRepository;
use Mdanter\Ecc\Legacy\Curves\CurveFactory;
use Mdanter\Ecc\Legacy\Primitives\GeneratorPoint;
use Mdanter\Ecc\Legacy\Serializer\Point\Format\CompressedPointSerializer;
use Mdanter\Ecc\Legacy\Serializer\Point\Format\UncompressedPointSerializer;
use Mdanter\Ecc\Math\UnsafeMath;
use Mdanter\Ecc\Primitives\Curve;
use Mdanter\Ecc\Serializer\PointSerializer;
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

        $curveRepository = new CurveRepository();
        foreach ($files as $file) {
            $curve = $curveRepository->resolveByName($file['curve']);
            foreach ($file['fixtures'] as $i => $fixture) {
                $datasetIdentifier = $file['file'] . "." . $i;

                $datasets[$datasetIdentifier] = [
                    $curve,
                    gmp_init((string)$fixture['k'], 10),
                    gmp_init($fixture['x'], 16),
                    gmp_init($fixture['y'], 16),
                    $file['curve'] === 'nistp224' // skip decompress for this curve, as not implemented
                ];
            }
        }

        return $datasets;
    }

    /**
     * @dataProvider getKeypairFixtures()
     */
    public function testGetPublicKey(Curve $curve, \GMP $k, \GMP $expectedX, \GMP $expectedY, bool $skipDecompress)
    {
        $math = new UnsafeMath($curve);

        $publicKey = $math->mul($curve->getG(), $k);

        $this->assertEquals($expectedX, $publicKey->x);
        $this->assertEquals($expectedY, $publicKey->y);

        $serializer = new PointSerializer($curve, PointSerializer::ENCODING_UNCOMPRESSED);
        $serialized = $serializer->serialize($publicKey);
        $parsed = $serializer->deserialize($serialized);
        $this->assertEquals($expectedX, $parsed->x);
        $this->assertEquals($expectedY, $parsed->y);

        $serializer = new PointSerializer($curve, PointSerializer::ENCODING_COMPRESSED);
        $serialized = $serializer->serialize($publicKey);
        if ($skipDecompress) {
            return;
        }
        $parsed = $serializer->deserialize($serialized);
        $this->assertEquals($expectedX, $parsed->x);
        $this->assertEquals($expectedY, $parsed->y);
    }
}

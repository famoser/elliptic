<?php

namespace Mdanter\Ecc\Integration\Rooterberg;

use Mdanter\Ecc\Crypto\Key\PublicKey;
use Mdanter\Ecc\Crypto\Signature\SignHasher;
use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Serializer\Point\Format\CompressedPointSerializer;

class FixturesRepository
{
    private static function readTestvectors(string $algorithm, string $testvectorsName): array
    {
        $path = __DIR__ . "/fixtures/test_vectors/{$algorithm}/{$algorithm}_{$testvectorsName}.json";
        $testvectorsJson = file_get_contents($path);
        if (!$testvectorsJson) {
            throw new \InvalidArgumentException("Failed to read test fixture file $path");
        }

        return json_decode($testvectorsJson, true);
    }

    private static function parseEcdsaSha224Testvectors(array $testvectors): array
    {
        $results = [];

        $algorithm = $testvectors['algorithm'];
        $generator = CurveFactory::getGeneratorByName($algorithm['curve']);

        assert($algorithm['sha'] === 'SHA-224');
        $signHasher = new SignHasher('sha224');

        $pointSerializer = new CompressedPointSerializer(new GmpMath());

        foreach ($testvectors['tests'] as $testvector) {
            $point = $pointSerializer->deserialize($generator->getCurve(), $testvector['publicKeyCompressed']);
            $publicKey = new PublicKey(new GmpMath(), $generator, $point);

            assert($testvector['sha'], 'SHA-256');

            $tcId = "tcId: " . $testvector['tcId'];

            $hash = $signHasher->makeHash(hex2bin($testvector['msg']), $generator);
            $results[$tcId] = [
                'generator' => $generator,
                'publicKey' => $publicKey,
                'hash' => $hash,
                'sig' => $testvector['sig'],
                'comment' => $testvector['comment'],
                'valid' => $testvector['valid'],
                'flags' => $testvector['flags'] ?? [],
            ];
        }

        return $results;
    }

    public static function createEcdsaSha224Fixtures(string $curve): array
    {
        $testvectors = FixturesRepository::readTestvectors("ecdsa", "{$curve}_sha_224_p1363");

        return FixturesRepository::parseEcdsaSha224Testvectors($testvectors);
    }
}

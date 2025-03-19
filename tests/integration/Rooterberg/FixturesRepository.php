<?php

namespace Famoser\Elliptic\Integration\Rooterberg;

use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Integration\Utils\Key\PublicKey;
use Famoser\Elliptic\Integration\Utils\Signature\SignHasher;
use Famoser\Elliptic\Serializer\PointDecoderException;
use Famoser\Elliptic\Serializer\PointSerializer;

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

    /**
     * @throws PointDecoderException
     */
    private static function parseEcdsaSha224Testvectors(array $testvectors): array
    {
        $results = [];

        $algorithm = $testvectors['algorithm'];
        $curveRepository = new CurveRepository();
        $curve = $curveRepository->findByName($algorithm['curve']);

        $pointSerializer = new PointSerializer($curve);

        foreach ($testvectors['tests'] as $testvector) {
            $publicKey = $pointSerializer->deserialize($testvector['publicKeyUncompressed']);

            $tcId = "tcId: " . $testvector['tcId'];
            $results[$tcId] = [
                'curve' => $curve,
                'publicKey' => $publicKey,
                'message' => hex2bin($testvector['msg']),
                'sig' => $testvector['sig'],
                'comment' => $testvector['comment'],
                'valid' => $testvector['valid'],
                'flags' => $testvector['flags'] ?? [],
            ];
        }

        return $results;
    }

    /**
     * @throws PointDecoderException
     */
    public static function createEcdsaSha224Fixtures(string $curve): array
    {
        $testvectors = FixturesRepository::readTestvectors("ecdsa", "{$curve}_sha_224_p1363");

        return FixturesRepository::parseEcdsaSha224Testvectors($testvectors);
    }
}

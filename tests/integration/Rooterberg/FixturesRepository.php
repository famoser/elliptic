<?php

namespace Famoser\Elliptic\Integration\Rooterberg;

use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Math\SWUnsafeMath;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use Famoser\Elliptic\Serializer\PointDecoder\SWPointDecoder;
use Famoser\Elliptic\Serializer\SECSerializer;
use Famoser\Elliptic\Serializer\SerializerException;

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
     * @throws PointDecoderException|SerializerException
     */
    private static function parseSWEcdsaTestvectors(array $testvectors): array
    {
        $results = [];

        $algorithm = $testvectors['algorithm'];
        $curveRepository = new CurveRepository();
        $curve = $curveRepository->findByName($algorithm['curve']);

        $math = new SWUnsafeMath($curve);
        $pointSerializer = new SECSerializer($math, new SWPointDecoder($curve));

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
     * @throws PointDecoderException|SerializerException
     */
    public static function createSWEcdsaFixtures(string $curve, int $shaSize): array
    {
        $testvectors = FixturesRepository::readTestvectors("ecdsa", "{$curve}_sha_{$shaSize}_p1363");

        return FixturesRepository::parseSWEcdsaTestvectors($testvectors);
    }
}

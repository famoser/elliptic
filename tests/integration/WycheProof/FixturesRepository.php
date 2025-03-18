<?php

namespace Mdanter\Ecc\Integration\WycheProof;

use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\Integration\Utils\Signature\SignHasher;

class FixturesRepository
{
    private static function readTestvectors(string $testvectorsName): array
    {
        $path = __DIR__ . "/fixtures/testvectors/{$testvectorsName}_test.json";
        $testvectorsJson = file_get_contents($path);
        if (!$testvectorsJson) {
            throw new \InvalidArgumentException("Failed to read test fixture file $path");
        }

        return json_decode($testvectorsJson, true);
    }

    private static function parseEcdhTestvectors(array $testvectors): array
    {
        $results = [];

        foreach ($testvectors['testGroups'] as $testGroup) {
            foreach ($testGroup['tests'] as $testvector) {
                $tcId = "tcId: " . $testvector['tcId'];

                $results[$tcId] = [
                    'comment' => $testvector['comment'],
                    'public' => $testvector['public'],
                    'private' => $testvector['private'],
                    'shared' => $testvector['shared'],
                    'result' => $testvector['result'],
                    'flags' => $testvector['flags'] ?? [],
                ];
            }
        }

        return $results;
    }

    private static function parseEcdsaSha256Testvectors(array $testvectors): array
    {
        $results = [];

        foreach ($testvectors['testGroups'] as $testGroup) {
            $key = $testGroup['key'];
            $generator = CurveFactory::getGeneratorByName($key['curve']);
            $publicKey = $generator->getPublicKeyFrom(gmp_init($key['wx'], 16), gmp_init($key['wy'], 16));

            assert($testGroup['sha'], 'SHA-256');
            $signHasher = new SignHasher('sha256');

            foreach ($testGroup['tests'] as $testvector) {
                $tcId = "tcId: " . $testvector['tcId'];

                $hash = $signHasher->makeHash(hex2bin($testvector['msg']), $generator);
                $results[$tcId] = [
                    'generator' => $generator,
                    'publicKey' => $publicKey,
                    'hash' => $hash,
                    'sig' => $testvector['sig'],
                    'comment' => $testvector['comment'],
                    'result' => $testvector['result'],
                    'flags' => $testvector['flags'] ?? [],
                ];
            }
        }

        return $results;
    }

    private static function filterEcdhFixtures(array $testvectors): array
    {
        $result = [];
        foreach ($testvectors as $key => $testvector) {
            // skip testing the ASN library
            if (in_array(WycheProofConstants::FLAG_INVALID_ASN, $testvector['flags'], true)) {
                continue;
            }

            // skip unnamed curves DER (as the DER encoding is just used for testing, not exposed outside)
            if (in_array(WycheProofConstants::FLAG_UNNAMED_CURVE, $testvector['flags'], true)) {
                continue;
            }

            // skip wrong curves
            if (str_starts_with($testvector['comment'], 'Public key uses wrong curve:')) {
                continue;
            }

            $result[$key] = $testvector;
        }

        return $result;
    }

    public static function createEcdhEcpointFixtures(string $curve): array
    {
        $testvectors = FixturesRepository::readTestvectors("ecdh_{$curve}_ecpoint");

        return FixturesRepository::parseEcdhTestvectors($testvectors);
    }

    public static function createFilteredEcdhFixtures(string $curve): array
    {
        $testvectors = FixturesRepository::readTestvectors("ecdh_{$curve}");
        $fixtures = FixturesRepository::parseEcdhTestvectors($testvectors);

        return self::filterEcdhFixtures($fixtures);
    }

    public static function createEcdsaSha256Fixtures(string $curve): array
    {
        $testvectors = FixturesRepository::readTestvectors("ecdsa_{$curve}_sha256_p1363");

        return FixturesRepository::parseEcdsaSha256Testvectors($testvectors);
    }
}

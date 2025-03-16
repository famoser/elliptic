<?php

namespace Mdanter\Ecc\Integration\WycheProof;

class FixturesRepository
{
    private static function readEcdhEcpointTestvectors(string $curve): array
    {
        $path = __DIR__ . "/fixtures/testvectors/ecdh_{$curve}_ecpoint_test.json";
        $testvectorsJson = file_get_contents($path);
        if (!$testvectorsJson) {
            throw new \InvalidArgumentException("Failed to read test fixture file $path");
        }

        return json_decode($testvectorsJson, true);
    }

    private static function createEcdhFixtures(array $testvectors): array
    {
        $results = [];

        assert(1 === count($testvectors['testGroups']));

        foreach ($testvectors['testGroups'][0]['tests'] as $testvector) {
            $tcId = "tcId: " . $testvector['tcId'];

            $results[$tcId] = [
                $testvector['comment'],
                $testvector['public'],
                $testvector['private'],
                $testvector['shared'],
                $testvector['result'],
                $testvector['flags'] ?? [],
            ];
        }

        return $results;
    }

    public static function createEcdhEcpointFixtures(string $curve): array
    {
        $testvectors = FixturesRepository::readEcdhEcpointTestvectors($curve);

        return FixturesRepository::createEcdhFixtures($testvectors);
    }
}

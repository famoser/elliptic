<?php

namespace Mdanter\Ecc\Integration\WycheProof;

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

    private static function createEcdhFixtures(array $testvectors): array
    {
        $results = [];

        assert(1 === count($testvectors['testGroups']));

        foreach ($testvectors['testGroups'][0]['tests'] as $testvector) {
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

        return FixturesRepository::createEcdhFixtures($testvectors);
    }

    public static function createFilteredEcdhFixtures(string $curve): array
    {
        $testvectors = FixturesRepository::readTestvectors("ecdh_{$curve}");
        $fixtures = FixturesRepository::createEcdhFixtures($testvectors);

        return self::filterEcdhFixtures($fixtures);
    }

    public static function assertExpectedExceptionEcdh()
    {

    }
}

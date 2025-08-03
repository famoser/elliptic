<?php

namespace Famoser\Elliptic\Tests\ConstTime\CollectorFactory;

use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Tests\ConstTime\Collector\MathCollector;
use Famoser\Elliptic\Tests\Integration\Rooterberg;
use Famoser\Elliptic\Tests\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Tests\Integration\Utils\MathRecoder;
use Famoser\Elliptic\Tests\Integration\WycheProof;
use Famoser\Elliptic\Tests\Integration\WycheProof\Utils\WycheProofConstants;

class EcdsaShaCollectorFactory
{
    private static function create(string $curveName, MathInterface $math, int $shaSize, array $fixtures): MathCollector
    {
        $recoder = new MathRecoder($math);
        $signer = new ECDSASigner($recoder, 'sha' . $shaSize);
        foreach ($fixtures as $key => $fixture) {
            $recoder->setContext($key);
            $signer->verify($fixture['publicKey'], $fixture['sig'], $fixture['message']);
        }

        return MathCollector::createFromRecordedMath($curveName, $recoder, $fixtures);
    }

    public static function createFromRooterberg(string $curveName, MathInterface $math, int $shaSize): MathCollector
    {
        $fixtures = Rooterberg\FixturesRepository::createSWEcdsaFixtures($curveName, $shaSize);
        $fixtures = array_filter($fixtures, static fn(array $fixture) => $fixture['valid']);

        return self::create($curveName, $math, $shaSize, $fixtures);
    }

    public static function createFromWycheSha256(string $curveName, MathInterface $math): MathCollector
    {
        $fixtures = WycheProof\Utils\FixturesRepository::createEcdsaSha256Fixtures($curveName);
        $fixtures = array_filter($fixtures, static fn(array $fixture) => $fixture['result'] === WycheProofConstants::RESULT_VALID);

        return self::create($curveName, $math, 256, $fixtures);
    }
}

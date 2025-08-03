<?php

namespace Famoser\Elliptic\Tests\ConstTime\CollectorFactory;

use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Tests\ConstTime\Collector\MathCollector;
use Famoser\Elliptic\Tests\Integration\Rooterberg;
use Famoser\Elliptic\Tests\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Tests\Integration\Utils\MathAdapter\MathRecoder;
use Famoser\Elliptic\Tests\Integration\Utils\MathAdapter\PhpeccMath;
use Famoser\Elliptic\Tests\Integration\WycheProof;
use Famoser\Elliptic\Tests\Integration\WycheProof\Utils\WycheProofConstants;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Optimized\BP256;

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

    public static function createPhpeccBrainpool256r1(Curve $brainpool256r1): MathCollector
    {
        $curve = EccFactory::getBrainpoolCurves()->optimizedCurve256r1();
        $math = new PhpeccMath($brainpool256r1, $curve, new BP256());

        $fixtures = WycheProof\Utils\FixturesRepository::createEcdsaSha256Fixtures('brainpoolP256r1');
        $fixtures = array_filter($fixtures, static fn(array $fixture) => $fixture['result'] === WycheProofConstants::RESULT_VALID);

        return self::create('brainpool256r1', $math, 256, $fixtures);
    }
}

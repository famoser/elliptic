<?php

namespace Famoser\Elliptic\Tests\ConstTime\CollectorFactory;

use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Tests\ConstTime\Collector\AbstractCollector;
use Famoser\Elliptic\Tests\ConstTime\Collector\MathCollector;
use Famoser\Elliptic\Tests\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Tests\Integration\Utils\EdDSA\AbstractEdDSASigner;
use Famoser\Elliptic\Tests\Integration\Utils\EdDSA\EdDSASignerEd25519;
use Famoser\Elliptic\Tests\Integration\Utils\EdDSA\EDDSASignerEd448;
use Famoser\Elliptic\Tests\Integration\Utils\MathRecoder;
use Famoser\Elliptic\Tests\Integration\WycheProof\Utils\FixturesRepository;
use Famoser\Elliptic\Tests\Integration\WycheProof\Utils\WycheProofConstants;

class EddsaCollectorFactory
{
    /**
     * @param callable(MathInterface): AbstractEdDSASigner $createSigner
     */
    private static function create(string $curveName, MathInterface $math, callable $createSigner, array $fixtures): MathCollector
    {
        $recoder = new MathRecoder($math);
        $signer = $createSigner($recoder);
        foreach ($fixtures as $key => $fixture) {
            $recoder->setContext($key);
            $signer->verify($fixture['public'], $fixture['sig'], $fixture['message']);
        }

        return MathCollector::createFromRecordedMath($curveName, $recoder, $fixtures);
    }

    public static function createEd25519(MathInterface $math): MathCollector
    {
        $fixtures = FixturesRepository::createEddsaFixtures('eddsa');
        $fixtures = array_filter($fixtures, static fn (array $fixture) => $fixture['result'] === WycheProofConstants::RESULT_VALID);
        $createSigner = static fn ($math) => new EdDSASignerEd25519($math);

        return self::create('ed25519', $math, $createSigner, $fixtures);
    }

    public static function createEd448(MathInterface $math): MathCollector
    {
        $fixtures = FixturesRepository::createEddsaFixtures('ed448');
        $fixtures = array_filter($fixtures, static fn (array $fixture) => $fixture['result'] === WycheProofConstants::RESULT_VALID);
        $createSigner = static fn ($math) => new EDDSASignerEd448($math);

        return self::create('ed448', $math, $createSigner, $fixtures);
    }
}

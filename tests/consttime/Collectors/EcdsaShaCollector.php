<?php

namespace Famoser\Elliptic\Tests\ConstTime\Collectors;

use Famoser\Elliptic\Tests\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Tests\Integration\WycheProof;
use Famoser\Elliptic\Tests\Integration\Rooterberg;
use Famoser\Elliptic\Tests\Integration\WycheProof\Utils\WycheProofConstants;
use Famoser\Elliptic\Math\MathInterface;

class EcdsaShaCollector extends AbstractCollector
{
    public function __construct(string $curveName, MathInterface $math, array $fixtures, private readonly ECDSASigner $signer)
    {
        $mathName = substr($math::class, strrpos($math::class, '\\') + 1);
        parent::__construct($curveName, $mathName, $fixtures);
    }

    public static function createFromRooterberg(string $curveName, int $shaSize, MathInterface $math): self
    {
        $fixtures = Rooterberg\FixturesRepository::createSWEcdsaFixtures($curveName, $shaSize);
        $fixtures = array_filter($fixtures, static fn (array $fixture) => $fixture['valid']);
        $signer = new ECDSASigner($math, 'sha' . $shaSize);

        return new self($curveName, $math, $fixtures, $signer);
    }

    public static function createFromWycheSha256(string $curveName, MathInterface $math): self
    {
        $fixtures = WycheProof\Utils\FixturesRepository::createEcdsaSha256Fixtures($curveName);
        $fixtures = array_filter($fixtures, static fn (array $fixture) => $fixture['result'] === WycheProofConstants::RESULT_VALID);
        $signer = new ECDSASigner($math, 'sha256');

        return new self($curveName, $math, $fixtures, $signer);
    }

    protected function runFixture(array $fixture): void
    {
        $this->signer->verify($fixture['publicKey'], $fixture['sig'], $fixture['message']);
    }
}

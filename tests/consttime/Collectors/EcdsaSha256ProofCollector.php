<?php

namespace Famoser\Elliptic\ConstTime\Collectors;

use Famoser\Elliptic\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Integration\WycheProof;
use Famoser\Elliptic\Integration\Rooterberg;
use Famoser\Elliptic\Math\MathInterface;

class EcdsaSha256ProofCollector extends AbstractProofCollector
{
    public function __construct(string $curveName, MathInterface $math, array $fixtures, private readonly ECDSASigner $signer)
    {
        parent::__construct($curveName, $math, $fixtures);
    }

    public static function createFromRooterberg(string $curveName, int $shaSize, MathInterface $math): self
    {
        $fixtures = Rooterberg\FixturesRepository::createSWEcdsaFixtures($curveName, $shaSize);
        $signer = new ECDSASigner($math, 'sha' . $shaSize);

        return new self($curveName, $math, $fixtures, $signer);
    }

    public static function createFromWyche(string $curveName, MathInterface $math): self
    {
        $fixtures = WycheProof\Utils\FixturesRepository::createEcdsaSha256Fixtures($curveName);
        $signer = new ECDSASigner($math, 'sha256');

        return new self($curveName, $math, $fixtures, $signer);
    }

    protected function runFixture(array $fixture): void
    {
        $this->signer->verify($fixture['publicKey'], $fixture['sig'], $fixture['message']);
    }
}

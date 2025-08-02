<?php

namespace Famoser\Elliptic\ConstTime\Collectors;

use Famoser\Elliptic\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Integration\Utils\EdDSA\AbstractEdDSASigner;
use Famoser\Elliptic\Integration\Utils\EdDSA\EdDSASignerEd25519;
use Famoser\Elliptic\Integration\Utils\EdDSA\EDDSASignerEd448;
use Famoser\Elliptic\Integration\WycheProof;
use Famoser\Elliptic\Integration\Rooterberg;
use Famoser\Elliptic\Integration\WycheProof\Utils\FixturesRepository;
use Famoser\Elliptic\Integration\WycheProof\Utils\WycheProofConstants;
use Famoser\Elliptic\Math\MathInterface;

class EddsaProofCollector extends AbstractProofCollector
{
    public function __construct(string $curveName, MathInterface $math, array $fixtures, private readonly AbstractEdDSASigner $signer)
    {
        $mathName = substr($math::class, strrpos($math::class, '\\') + 1);
        parent::__construct($curveName, $mathName, $fixtures);
    }

    public static function createEd25519(MathInterface $math): self
    {
        $fixtures = FixturesRepository::createEddsaFixtures('eddsa');
        $fixtures = array_filter($fixtures, static fn (array $fixture) => $fixture['result'] === WycheProofConstants::RESULT_VALID);
        $signer = new EdDSASignerEd25519($math);

        return new self('ed25519', $math, $fixtures, $signer);
    }

    public static function createEd448(MathInterface $math): self
    {
        $fixtures = FixturesRepository::createEddsaFixtures('ed448');
        $fixtures = array_filter($fixtures, static fn (array $fixture) => $fixture['result'] === WycheProofConstants::RESULT_VALID);
        $signer = new EDDSASignerEd448($math);

        return new self('ed448', $math, $fixtures, $signer);
    }

    protected function runFixture(array $fixture): void
    {
        $this->signer->verify($fixture['public'], $fixture['sig'], $fixture['message']);
    }
}

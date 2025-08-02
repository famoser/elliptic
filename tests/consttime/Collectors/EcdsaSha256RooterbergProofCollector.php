<?php

namespace Famoser\Elliptic\ConstTime\Collectors;

use Famoser\Elliptic\Integration\Rooterberg\FixturesRepository;
use Famoser\Elliptic\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Math\MathInterface;

class EcdsaSha256RooterbergProofCollector extends EcdsaSha256ProofCollector
{
    public function __construct(string $curveName, int $shaSize, MathInterface $math)
    {
        $fixtures = FixturesRepository::createSWEcdsaFixtures($curveName, $shaSize);
        $signer = new ECDSASigner($math, 'sha' . $shaSize);

        parent::__construct($curveName, $signer, $math, $fixtures);
    }
}

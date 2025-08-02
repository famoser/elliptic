<?php

namespace Famoser\Elliptic\ConstTime\Collectors;

use Famoser\Elliptic\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Integration\WycheProof\Utils\FixturesRepository;
use Famoser\Elliptic\Integration\WycheProof\Utils\WycheProofConstants;
use Famoser\Elliptic\Math\MathInterface;

class EcdsaSha256WycheProofCollector extends EcdsaSha256ProofCollector
{
    public function __construct(string $curveName, MathInterface $math)
    {
        $signer = new ECDSASigner($math, 'sha256');
        $fixtures = FixturesRepository::createEcdsaSha256Fixtures($curveName);

        parent::__construct($curveName, $signer, $math, $fixtures);
    }
}

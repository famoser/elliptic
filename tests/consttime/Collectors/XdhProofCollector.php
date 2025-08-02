<?php

namespace Famoser\Elliptic\ConstTime\Collectors;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Integration\Utils\ECDSASigner;
use Famoser\Elliptic\Integration\WycheProof;
use Famoser\Elliptic\Integration\Rooterberg;
use Famoser\Elliptic\Integration\WycheProof\Utils\FixturesRepository;
use Famoser\Elliptic\Integration\WycheProof\Utils\WycheProofConstants;
use Famoser\Elliptic\Math\Calculator\AbstractCalculator;
use Famoser\Elliptic\Math\Calculator\MGXCalculator;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;

class XdhProofCollector extends AbstractProofCollector
{
    public function __construct(string $curveName, array $fixtures, private readonly MGXCalculator $calculator, private readonly RFC7784Decoder $encoder, private readonly int $bits)
    {
        parent::__construct($curveName, 'MGXCalculator', $fixtures);
    }

    public static function createForCurve25519(): self
    {
        $fixtures = FixturesRepository::createFilteredXdhFixtures('x25519');
        $calculator = new MGXCalculator(BernsteinCurveFactory::curve25519());

        return new self('curve25519', $fixtures, $calculator, new RFC7784Decoder(), 255);
    }

    protected function runFixture(array $fixture): void
    {
        $publicU = $this->encoder->decodeUCoordinate($fixture['public'], $this->bits);
        $decodedPrivate = $this->encoder->decodeScalar25519($fixture['private']);

        $sharedSecret = $this->calculator->mul($publicU, $decodedPrivate);

        $this->encoder->encodeUCoordinate($sharedSecret, $this->bits);
    }
}

<?php

namespace Famoser\Elliptic\Tests\ConstTime\Collectors;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Tests\Integration\WycheProof\Utils\FixturesRepository;
use Famoser\Elliptic\Math\Calculator\MGXCalculator;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;

class XdhCalculatorCollector extends AbstractCollector
{
    public function __construct(string $curveName, array $fixtures, private readonly MGXCalculator $calculator)
    {
        parent::__construct($curveName, 'MGXCalculator', $fixtures);
    }

    public static function createForCurve25519(MGXCalculator $calculator): self
    {
        $decoder = new RFC7784Decoder();
        $fixtures = array_map(
            function (array $fixture) use ($decoder) {
                $fixture['publicU'] = $decoder->decodeUCoordinate($fixture['public'], 255);
                $fixture['privateDecoded'] = $decoder->decodeScalar25519($fixture['private']);

                return $fixture;
            },
            FixturesRepository::createFilteredXdhFixtures('x25519')
        );

        return new self('curve25519', $fixtures, $calculator);
    }

    protected function runFixture(array $fixture): void
    {
        $this->calculator->mul($fixture['publicU'], $fixture['privateDecoded']);
    }
}

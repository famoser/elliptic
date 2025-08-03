<?php

namespace Famoser\Elliptic\Tests\ConstTime\Collector;

use Famoser\Elliptic\Math\Calculator\MGXCalculator;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use Famoser\Elliptic\Tests\Integration\WycheProof\Utils\FixturesRepository;

class MGXCalculatorCollector extends AbstractCollector
{
    private function __construct(string $curveName, private readonly MGXCalculator $calculator, array $fixtures)
    {
        parent::__construct($curveName, 'MGXCalculator', $fixtures);
    }

    public static function createFromRawFixtures(string $curveName, MGXCalculator $calculator, array $fixtures): self
    {
        return new self($curveName, $calculator, $fixtures);
    }

    protected function runFixture(array $fixture): void
    {
        $this->calculator->mul($fixture['u'], $fixture['factor']);
    }
}

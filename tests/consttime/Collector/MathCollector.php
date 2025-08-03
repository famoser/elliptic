<?php

namespace Famoser\Elliptic\Tests\ConstTime\Collector;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use Famoser\Elliptic\Serializer\PointDecoder\MGPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use Famoser\Elliptic\Tests\Integration\Utils\MathRecoder;
use Famoser\Elliptic\Tests\Integration\WycheProof\Utils\FixturesRepository;
use TypeError;

class MathCollector extends AbstractCollector
{
    private function __construct(string $curveName, private readonly MathInterface $math, array $fixtures)
    {
        $mathName = substr($math::class, strrpos($math::class, '\\') + 1);
        parent::__construct($curveName, $mathName, $fixtures);
    }

    public static function createFromRecordedMath(string $curveName, MathRecoder $recoder, array $fixtures): self
    {
        $cleanFixtures = [];
        foreach ($recoder->getOperations() as $context => $operations) {
            $fixture = $fixtures[$context];
            foreach ($operations as $index => $operation) {
                $identifier = $context . "_" . $index;
                if ($operation[0] == 'mul') {
                    $cleanFixtures[$identifier] = ['point' => $operation[1][0], 'factor' => $operation[1][1], 'flags' => $fixture['flags']];
                } elseif ($operation[0] == 'mulG') {
                    $cleanFixtures[$identifier] = ['point' => $recoder->getMath()->getCurve()->getG(), 'factor' => $operation[1][0], 'flags' => $fixture['flags']];
                }
            }
        }

        return new self($curveName, $recoder->getMath(), $cleanFixtures);
    }

    public static function createForRawFixtures(string $curveName, MathInterface $math, array $fixtures): self
    {
        return new self($curveName, $math, $fixtures);
    }

    protected function runFixture(array $fixture): void
    {
        $this->math->mul($fixture['point'], $fixture['factor']);
    }
}

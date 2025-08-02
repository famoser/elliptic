<?php

namespace Famoser\Elliptic\Tests\ConstTime\Collectors;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Tests\Integration\WycheProof\Utils\FixturesRepository;
use Famoser\Elliptic\Math\Calculator\MGXCalculator;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use Famoser\Elliptic\Serializer\PointDecoder\MGPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use TypeError;

class XdhMathCollector extends AbstractCollector
{
    public function __construct(string $curveName, array $fixtures, private readonly MathInterface $math)
    {
        $mathName = substr($math::class, strrpos($math::class, '\\') + 1);
        parent::__construct($curveName, $mathName, $fixtures);
    }

    public static function createForCurve25519(MathInterface $math): self
    {
        $decoder = new RFC7784Decoder();

        $curve = BernsteinCurveFactory::curve25519();
        $pointDecoder = new MGPointDecoder($curve);

        $fixtures = array_filter(
            array_map(
                function (array $fixture) use ($decoder, $pointDecoder, $math) {
                    $publicU = $decoder->decodeUCoordinate($fixture['public'], 255);

                    try {
                        $fixture['publicPoint'] = $pointDecoder->fromXCoordinate($publicU);
                        $fixture['privateDecoded'] = $decoder->decodeScalar25519($fixture['private']);

                        $math->mul($fixture['publicPoint'], $fixture['privateDecoded']);
                    } catch (PointDecoderException|TypeError) {
                        return null;
                    }

                    return $fixture;
                },
                FixturesRepository::createFilteredXdhFixtures('x25519')
            )
        );

        return new self('curve25519', $fixtures, $math);
    }

    protected function runFixture(array $fixture): void
    {
        $this->math->mul($fixture['publicPoint'], $fixture['privateDecoded']);
    }
}

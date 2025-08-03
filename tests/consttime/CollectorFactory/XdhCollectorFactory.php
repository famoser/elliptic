<?php

namespace Famoser\Elliptic\Tests\ConstTime\CollectorFactory;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Math\Calculator\MGXCalculator;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use Famoser\Elliptic\Serializer\PointDecoder\MGPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use Famoser\Elliptic\Tests\ConstTime\Collector\MathCollector;
use Famoser\Elliptic\Tests\ConstTime\Collector\MGXCalculatorCollector;
use Famoser\Elliptic\Tests\Integration\WycheProof\Utils\FixturesRepository;
use TypeError;

class XdhCollectorFactory
{
    public static function createForCurve25519Calculator(MGXCalculator $calculator): MGXCalculatorCollector
    {
        $decoder = new RFC7784Decoder();
        $fixtures = array_map(
            function (array $fixture) use ($decoder) {
                return [
                    'flags' => $fixture['flags'],
                    'u' => $decoder->decodeUCoordinate($fixture['public'], 255),
                    'factor' => $decoder->decodeScalar25519($fixture['private'])
                ];
            },
            FixturesRepository::createFilteredXdhFixtures('x25519')
        );

        return MGXCalculatorCollector::createFromRawFixtures('curve25519', $calculator, $fixtures);
    }

    public static function createForCurve25519Math(MathInterface $math): MathCollector
    {
        $decoder = new RFC7784Decoder();

        $curve = BernsteinCurveFactory::curve25519();
        $pointDecoder = new MGPointDecoder($curve);

        $fixtures = array_filter(
            array_map(
                function (array $fixture) use ($decoder, $pointDecoder, $math) {
                    $publicU = $decoder->decodeUCoordinate($fixture['public'], 255);

                    $res = [
                        'flags' => $fixture['flags'],
                        'factor' => $decoder->decodeScalar25519($fixture['private'])
                    ];
                    try {
                        $res['point'] = $pointDecoder->fromXCoordinate($publicU);

                        $math->mul($res['point'], $res['factor']);
                    } catch (PointDecoderException | TypeError) {
                        return null;
                    }

                    return $res;
                },
                FixturesRepository::createFilteredXdhFixtures('x25519')
            )
        );

        return MathCollector::createForRawFixtures('curve25519', $math, $fixtures);
    }
}

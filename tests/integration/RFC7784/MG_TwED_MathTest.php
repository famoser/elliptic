<?php

namespace Famoser\Elliptic\Integration\RFC7784;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Math\MG_TwED_ANeg1_Math;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use Famoser\Elliptic\Serializer\PointDecoder\MGPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use PHPUnit\Framework\TestCase;

class MG_TwED_MathTest extends TestCase
{
    use RFC7784TestVectorsTrait;

    /**
     * @dataProvider vectors25519
     */
    public function testTestVectors25519OnTwED(string $scalar, string $u, string $expectedResult): void
    {
        $curve = BernsteinCurveFactory::curve25519();
        $birationalMapping = BernsteinCurveFactory::curve25519ToEdwards25519();
        $twistedEdwardsCurve = BernsteinCurveFactory::edwards25519();
        $calculator = new MG_TwED_ANeg1_Math($curve, $birationalMapping, $twistedEdwardsCurve);

        $decoder = new RFC7784Decoder();
        $scalar = $decoder->decodeScalar25519($scalar);
        $u = $decoder->decodeUCoordinate($u, 255);

        try {
            $mgDecoder = new MGPointDecoder($curve);
            $uPoint = $mgDecoder->fromXCoordinate($u, true);
        } catch (PointDecoderException) {
            $this->markTestSkipped();
        }

        $result = $calculator->mul($uPoint, $scalar);
        $encodedResult = $decoder->encodeUCoordinate($result->x, 255);
        $this->assertEquals($expectedResult, $encodedResult);
    }
}

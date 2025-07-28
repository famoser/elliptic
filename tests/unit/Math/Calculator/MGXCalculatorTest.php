<?php

namespace Famoser\Elliptic\Tests\Math\Calculator;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Integration\RFC7784\RFC7784TestVectorsTrait;
use Famoser\Elliptic\Math\Calculator\MGXCalculator;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use PHPUnit\Framework\TestCase;

class MGXCalculatorTest extends TestCase
{
    public function testTestVectors25519(): void
    {
        // https://datatracker.ietf.org/doc/html/rfc7748#section-5.2 X25519 (1/2)
        $scalar = 'a546e36bf0527c9d3b16154b82465edd62144c0ac1fc5a18506a2244ba449ac4';
        $u = 'e6db6867583030db3594c1a424b15f7c726624ec26b3353b10a903a6d0ab1c4c';
        $expectedResult = 'c3da55379de9c6908e94ea4df28d084f32eccf03491c71f754b4075577a28552';

        $curve = BernsteinCurveFactory::curve25519();
        $calculator = new MGXCalculator($curve);

        $decoder = new RFC7784Decoder();
        $scalar = $decoder->decodeScalar25519($scalar);
        $u = $decoder->decodeUCoordinate($u, 255);

        $result = $calculator->mul($u, $scalar);
        $encodedResult = $decoder->encodeUCoordinate($result, 255);
        $this->assertEquals($expectedResult, $encodedResult);
    }
}

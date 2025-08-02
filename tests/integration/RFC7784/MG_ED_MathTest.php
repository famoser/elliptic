<?php

namespace Famoser\Elliptic\Tests\Integration\RFC7784;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Tests\Integration\ExpensiveMath\UnresolvedErrorTrait;
use Famoser\Elliptic\Math\MG_ED_Math;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use Famoser\Elliptic\Serializer\PointDecoder\MGPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use PHPUnit\Framework\TestCase;

class MG_ED_MathTest extends TestCase
{
    use RFC7784TestVectorsTrait;
    use UnresolvedErrorTrait;

    /**
     * @dataProvider vectors448
     */
    public function testTestVectors448OnEd(string $scalar, string $u, string $expectedResult): void
    {
        $this->skipUnresolvedError(__CLASS__, __FUNCTION__);

        $curve = BernsteinCurveFactory::curve448();
        $birationalMapping = BernsteinCurveFactory::curve448ToEdwards();
        $edwardsCurve = BernsteinCurveFactory::curve448Edwards();
        $calculator = new MG_ED_Math($curve, $birationalMapping, $edwardsCurve);

        $decoder = new RFC7784Decoder();
        $scalar = $decoder->decodeScalar448($scalar);
        $u = $decoder->decodeUCoordinate($u, 448);

        try {
            $pointDecoder = new MGPointDecoder($curve);
            $uPoint = $pointDecoder->fromXCoordinate($u, true);
        } catch (PointDecoderException) {
            $this->markTestSkipped();
        }

        $result = $calculator->mul($uPoint, $scalar);
        $encodedResult = $decoder->encodeUCoordinate($result->x, 448);
        $this->assertEquals($expectedResult, $encodedResult);
    }
}

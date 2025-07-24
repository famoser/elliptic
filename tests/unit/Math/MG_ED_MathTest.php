<?php

namespace Famoser\Elliptic\Tests\Math;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Math\MG_ED_Math;
use Famoser\Elliptic\Math\MGUnsafeMath;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use Famoser\Elliptic\Serializer\PointDecoder\MGPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use Famoser\Elliptic\Tests\Math\Calculator\RFC7784TestVectorsTrait;
use PHPUnit\Framework\TestCase;

class MG_ED_MathTest extends TestCase
{
    use RFC7784TestVectorsTrait;

    /**
     * @dataProvider vectors448
     */
    public function testTestVectors448OnEd(string $scalar, string $u, string $expectedResult): void
    {
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

    public function testCurve448(): void
    {
        $curve = BernsteinCurveFactory::curve448();
        $birationalMapping = BernsteinCurveFactory::curve448ToEdwards();
        $edwardsCurve = BernsteinCurveFactory::curve448Edwards();
        $math = new MG_ED_Math($curve, $birationalMapping, $edwardsCurve);
        $baselineMath = new MGUnsafeMath($curve);

        $factor = gmp_init('599189175373896402783756016145213256157230856085026129926891459468622403380588640249457727683869421921443004045221642549886377526240828', 10);
        $actual = $math->mul($curve->getG(), $factor);
        $actualBaseline = $baselineMath->mul($curve->getG(), $factor);
        $expected = new Point(
            gmp_init('356464349418515164077825580275797994008614129675071795735859787967236123872832230826634350987952927395289262393527422212823415563783431', 10),
            gmp_init('130052194822882327916107803584377361768737051523812872835339292941462025016445220505950437485736657271324323672091546511573904787985802', 10)
        );

        $this->assertObjectEquals($expected, $actualBaseline);
        $this->assertObjectEquals($expected, $actual);
    }
}

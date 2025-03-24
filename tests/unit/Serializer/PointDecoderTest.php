<?php

namespace Famoser\Elliptic\Tests\Serializer;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoder;
use Famoser\Elliptic\Serializer\PointDecoderException;
use PHPUnit\Framework\TestCase;

class PointDecoderTest extends TestCase
{
    public function testFromCoordinatesCreatesPoint(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $decoder = new PointDecoder($curve);

        $expectedPoint = $curve->getG();
        $actualPoint = $decoder->fromCoordinates($expectedPoint->x, $expectedPoint->y);

        $this->assertEquals($expectedPoint, $actualPoint);
    }

    public static function invalidXYPoints(): array
    {
        $curve = SEC2CurveFactory::secp192r1();
        $expectedPoint = $curve->getG();

        $one = gmp_init(1);
        $unsupportedCurve = new Curve(CurveType::Montgomery, $curve->getP(), $curve->getA(), $curve->getB(), $curve->getG(), $curve->getN(), $curve->getH());
        return [
            [$curve, $one, $expectedPoint->y],
            [$curve, $expectedPoint->x, $one],
            [$curve, $one, $one],
            [$unsupportedCurve, $expectedPoint->x, $expectedPoint->y]
        ];
    }

    /**
     * @dataProvider invalidXYPoints
     */
    public function testFromCoordinatesChecksCurveEquation(Curve $curve, \GMP $x, \GMP $y): void
    {
        $this->expectException(PointDecoderException::class);

        $decoder = new PointDecoder($curve);

        $decoder->fromCoordinates($x, $y);
    }
}

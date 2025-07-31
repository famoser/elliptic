<?php

namespace Famoser\Elliptic\Tests\Serializer\PointDecoder;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use Famoser\Elliptic\Serializer\PointDecoder\SWPointDecoder;
use PHPUnit\Framework\TestCase;

class SWPointDecoderTest extends TestCase
{
    public static function invalidXYPoints(): array
    {
        $curve = SEC2CurveFactory::secp192r1();
        $expectedPoint = $curve->getG();

        $one = gmp_init(1);
        return [
            [$curve, $one, $expectedPoint->y],
            [$curve, $expectedPoint->x, $one],
            [$curve, $one, $one],
        ];
    }

    /**
     * @dataProvider invalidXYPoints
     */
    public function testFromCoordinatesChecksCurveEquation(Curve $curve, \GMP $x, \GMP $y): void
    {
        $this->expectException(PointDecoderException::class);

        $decoder = new SWPointDecoder($curve);

        $decoder->fromCoordinates($x, $y);
    }

    public function testFromXCoordinateRejectsInvalidXCoordinates(): void
    {
        $this->expectException(PointDecoderException::class);

        $curve = SEC2CurveFactory::secp192r1();
        $decoder = new SWPointDecoder($curve);

        $decoder->fromXCoordinate(gmp_init(1), true);
    }

    public static function curvesWithoutEasyPointReconstruction(): array
    {
        return [
            [SEC2CurveFactory::secp224k1()],
            [SEC2CurveFactory::secp224r1()]
        ];
    }

    /**
     * @dataProvider curvesWithoutEasyPointReconstruction
     */
    public function testRejectsCurvesWithoutEasyPointReconstruction(Curve $curve): void
    {
        $this->expectException(PointDecoderException::class);

        $decoder = new SWPointDecoder($curve);

        $decoder->fromXCoordinate(gmp_init(1), true);
    }
}

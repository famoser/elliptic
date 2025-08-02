<?php

namespace Famoser\Elliptic\Tests\Unit\Serializer\PointDecoder;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Serializer\PointDecoder\MGPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use PHPUnit\Framework\TestCase;

class MGPointDecoderTest extends TestCase
{
    public static function invalidXYPoints(): array
    {
        $curve = BernsteinCurveFactory::curve25519();
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

        $decoder = new MGPointDecoder($curve);

        $decoder->fromCoordinates($x, $y);
    }

    /**
     * @throws PointDecoderException
     */
    public function testFromXCoordinateRejectsInvalidXCoordinates(): void
    {
        $this->expectException(PointDecoderException::class);

        $curve = BernsteinCurveFactory::curve25519();
        $decoder = new MGPointDecoder($curve);

        $decoder->fromXCoordinate(gmp_init(2));
    }
}

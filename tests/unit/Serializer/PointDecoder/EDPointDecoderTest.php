<?php

namespace Famoser\Elliptic\Tests\Serializer\PointDecoder;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Serializer\PointDecoder\EDPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use Famoser\Elliptic\Serializer\PointDecoder\TwEDPointDecoder;
use PHPUnit\Framework\TestCase;

class EDPointDecoderTest extends TestCase
{
    public static function invalidXYPoints(): array
    {
        $curve = BernsteinCurveFactory::edwards448();
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

        $decoder = new EDPointDecoder($curve);

        $decoder->fromCoordinates($x, $y);
    }

    /**
     * @throws PointDecoderException
     */
    public function testFromXCoordinateRejectsInvalidXCoordinates(): void
    {
        $this->expectException(PointDecoderException::class);

        $curve = BernsteinCurveFactory::edwards448();
        $decoder = new EDPointDecoder($curve);

        $decoder->fromXCoordinate(gmp_init(2));
    }
}

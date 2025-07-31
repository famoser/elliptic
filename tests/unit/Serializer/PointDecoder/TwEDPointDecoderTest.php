<?php

namespace Famoser\Elliptic\Tests\Serializer\PointDecoder;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Serializer\PointDecoder\MGPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use Famoser\Elliptic\Serializer\PointDecoder\TwEDPointDecoder;
use PHPUnit\Framework\TestCase;

class TwEDPointDecoderTest extends TestCase
{
    public function testFromCoordinatesCreatesPoint(): void
    {
        $curve = BernsteinCurveFactory::edwards25519();
        $decoder = new TwEDPointDecoder($curve);

        $expectedPoint = $curve->getG();
        $actualPoint = $decoder->fromCoordinates($expectedPoint->x, $expectedPoint->y);

        $this->assertTrue($expectedPoint->equals($actualPoint));
    }

    public function testFromXCoordinatesCreatesPoint(): void
    {
        $curve = BernsteinCurveFactory::edwards25519();
        $decoder = new TwEDPointDecoder($curve);

        $expectedPoint = $curve->getG();
        $actualPoint = $decoder->fromXCoordinate($expectedPoint->x);

        $decoder->fromCoordinates($actualPoint->x, $actualPoint->y);
        $this->assertEquals(0, gmp_cmp($actualPoint->x, $expectedPoint->x));

        $squareActualY = gmp_mod(gmp_mul($actualPoint->y, $actualPoint->y), $curve->getP());
        $squareExpectedY = gmp_mod(gmp_mul($expectedPoint->y, $expectedPoint->y), $curve->getP());
        $this->assertEquals(0, gmp_cmp($squareActualY, $squareExpectedY));
    }

    public static function invalidXYPoints(): array
    {
        $curve = BernsteinCurveFactory::edwards25519();
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

        $decoder = new TwEDPointDecoder($curve);

        $decoder->fromCoordinates($x, $y);
    }

    /**
     * @throws PointDecoderException
     */
    public function testFromXCoordinateRejectsInvalidXCoordinates(): void
    {
        $this->expectException(PointDecoderException::class);

        $curve = BernsteinCurveFactory::edwards25519();
        $decoder = new TwEDPointDecoder($curve);

        $decoder->fromXCoordinate(gmp_init(3));
    }
}

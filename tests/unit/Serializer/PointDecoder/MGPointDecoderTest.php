<?php

namespace Famoser\Elliptic\Tests\Serializer\PointDecoder;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoder\MGPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use Famoser\Elliptic\Tests\TestUtils\CurveBuilder;
use PHPUnit\Framework\TestCase;

class MGPointDecoderTest extends TestCase
{
    public static function mgCurves(): array
    {
        return [
            [BernsteinCurveFactory::curve25519()],
            [BernsteinCurveFactory::curve448()]
        ];
    }

    /**
     * @dataProvider mgCurves
     */
    public function testFromCoordinatesCreatesPoint(Curve $curve): void
    {
        $decoder = new MGPointDecoder($curve);

        $expectedPoint = $curve->getG();
        $actualPoint = $decoder->fromCoordinates($expectedPoint->x, $expectedPoint->y);

        $this->assertTrue($expectedPoint->equals($actualPoint));
    }

    public function testFromXCoordinatesCreatesPointCurve25519(): void
    {
        $curve = BernsteinCurveFactory::curve25519();
        $decoder = new MGPointDecoder($curve);

        $expectedPoint = $curve->getG();
        $actualPoint = $decoder->fromXCoordinate($expectedPoint->x);

        $this->assertNotNull($decoder->fromCoordinates($actualPoint->x, $actualPoint->y));
        $this->assertEquals(0, gmp_cmp($actualPoint->x, $expectedPoint->x));

        $squareActualY = gmp_mod(gmp_mul($actualPoint->y, $actualPoint->y), $curve->getP());
        $squareExpectedY = gmp_mod(gmp_mul($expectedPoint->y, $expectedPoint->y), $curve->getP());
        $this->assertEquals(0, gmp_cmp($squareActualY, $squareExpectedY));
    }

    public function testFromXCoordinatesCreatesPointCurve448(): void
    {
        $curve = BernsteinCurveFactory::curve448();
        $decoder = new MGPointDecoder($curve);

        $expectedPoint = $curve->getG();
        $actualPoint = $decoder->fromXCoordinate($expectedPoint->x, true);
        $actualPointInverted = $decoder->fromXCoordinate($expectedPoint->x, false);

        $this->assertEquals(0, gmp_cmp($actualPoint->x, $expectedPoint->x));
        $this->assertEquals(0, gmp_cmp($actualPointInverted->x, $expectedPoint->x));

        $this->assertEquals(0, gmp_cmp($actualPoint->y, $expectedPoint->y));
        $this->assertNotEquals(0, gmp_cmp($actualPointInverted->y, $expectedPoint->y));

        $squareActualY = gmp_mod(gmp_mul($actualPointInverted->y, $actualPointInverted->y), $curve->getP());
        $squareExpectedY = gmp_mod(gmp_mul($expectedPoint->y, $expectedPoint->y), $curve->getP());
        $this->assertEquals(0, gmp_cmp($squareActualY, $squareExpectedY));
    }

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

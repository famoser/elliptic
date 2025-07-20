<?php

namespace Famoser\Elliptic\Tests\Serializer\PointDecoder;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Serializer\PointDecoder\MGPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use Famoser\Elliptic\Tests\TestUtils\CurveBuilder;
use PHPUnit\Framework\TestCase;

class MGPointDecoderTest extends TestCase
{
    /**
     * @throws PointDecoderException
     */
    public function testFromCoordinatesCreatesPoint(): void
    {
        $curve = BernsteinCurveFactory::curve25519();
        $decoder = new MGPointDecoder($curve);

        $expectedPoint = $curve->getG();
        $actualPoint = $decoder->fromCoordinates($expectedPoint->x, $expectedPoint->y);

        $this->assertTrue($expectedPoint->equals($actualPoint));
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

    public function testFromCoordinatesChecksCurveType(): void
    {
        $curve = BernsteinCurveFactory::curve25519();
        $unsupportedCurve = (new CurveBuilder($curve))->withType(CurveType::ShortWeierstrass)->build();

        $this->expectException(\AssertionError::class);

        new MGPointDecoder($unsupportedCurve);
    }

    /**
     * @throws PointDecoderException
     */
    public function testFromXCoordinatesCreatesPoint(): void
    {
        $curve = BernsteinCurveFactory::curve25519();
        $decoder = new MGPointDecoder($curve);

        $actualPoint = $decoder->fromXCoordinate($curve->getG()->x);

        $this->assertEquals(0, gmp_cmp($actualPoint->x, $curve->getG()->x));
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

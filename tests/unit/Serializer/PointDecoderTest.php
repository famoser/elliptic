<?php

namespace Famoser\Elliptic\Tests\Serializer;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Serializer\PointDecoder;
use Famoser\Elliptic\Serializer\PointDecoderException;
use Famoser\Elliptic\Tests\TestUtils\CurveBuilder;
use PHPUnit\Framework\TestCase;

class PointDecoderTest extends TestCase
{
    /**
     * @throws PointDecoderException
     */
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
        $unsupportedCurve = (new CurveBuilder($curve))->withType(CurveType::Montgomery)->build();
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

    /**
     * @throws PointDecoderException
     */
    public function testFromXCoordinatesCreatesPoint(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $decoder = new PointDecoder($curve);

        $expectedPoint = $curve->getG();
        $isEvenY = gmp_cmp(gmp_mod($expectedPoint->y, 2), 0) === 0;
        $actualPoint = $decoder->fromXCoordinate($expectedPoint->x, $isEvenY);

        $this->assertEquals($expectedPoint, $actualPoint);
    }

    /**
     * @throws PointDecoderException
     */
    public function testFromXCoordinatesRespectsSignBit(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $decoder = new PointDecoder($curve);

        $expectedPoint = $curve->getG();
        $isEvenY = gmp_cmp(gmp_mod($expectedPoint->y, 2), 0) === 0;
        $actualPoint = $decoder->fromXCoordinate($expectedPoint->x, !$isEvenY);

        $this->assertFalse($expectedPoint->equals($actualPoint));
    }

    /**
     * @throws PointDecoderException
     */
    public function testFromXCoordinateRejectsInvalidXCoordinates(): void
    {
        $this->expectException(PointDecoderException::class);

        $curve = SEC2CurveFactory::secp192r1();
        $decoder = new PointDecoder($curve);

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
    public function testRejectsInvalidCurves(Curve $curve): void
    {
        $this->expectException(PointDecoderException::class);

        $decoder = new PointDecoder($curve);

        $decoder->fromXCoordinate(gmp_init(1), true);
    }
}

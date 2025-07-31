<?php

namespace Famoser\Elliptic\Tests\Serializer\PointDecoder;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;
use Famoser\Elliptic\Serializer\PointDecoder\SWPointDecoder;
use Famoser\Elliptic\Tests\TestUtils\CurveBuilder;
use PHPUnit\Framework\TestCase;

class SWPointDecoderTest extends TestCase
{
    /**
     * @throws PointDecoderException
     */
    public function testFromCoordinatesCreatesPoint(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $decoder = new SWPointDecoder($curve);

        $expectedPoint = $curve->getG();
        $actualPoint = $decoder->fromCoordinates($expectedPoint->x, $expectedPoint->y);

        $this->assertTrue($expectedPoint->equals($actualPoint));
    }

    public function testFromXCoordinatesCreatesPoint(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $decoder = new SWPointDecoder($curve);

        $expectedPoint = $curve->getG();
        $isEvenY = gmp_cmp(gmp_mod($expectedPoint->y, 2), 0) === 0;
        $actualPoint = $decoder->fromXCoordinate($expectedPoint->x, $isEvenY);

        $this->assertTrue($expectedPoint->equals($actualPoint));
    }

    public function testFromXCoordinatesRespectsSignBit(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $decoder = new SWPointDecoder($curve);

        $expectedPoint = $curve->getG();
        $isEvenY = gmp_cmp(gmp_mod($expectedPoint->y, 2), 0) === 0;

        $actualPoint = $decoder->fromXCoordinate($expectedPoint->x, $isEvenY);
        $this->assertTrue($expectedPoint->equals($actualPoint));

        $actualPoint = $decoder->fromXCoordinate($expectedPoint->x, !$isEvenY);
        $this->assertFalse($expectedPoint->equals($actualPoint));
    }

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

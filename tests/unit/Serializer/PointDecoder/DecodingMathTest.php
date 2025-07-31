<?php

namespace Famoser\Elliptic\Tests\Serializer\PointDecoder;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Serializer\PointDecoder\MGPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderInterface;
use Famoser\Elliptic\Serializer\PointDecoder\SWPointDecoder;
use Famoser\Elliptic\Serializer\PointDecoder\TwEDPointDecoder;
use PHPUnit\Framework\TestCase;

class DecodingMathTest extends TestCase
{
    public static function pointDecoders(): array
    {
        return [
            [new MGPointDecoder(BernsteinCurveFactory::curve25519())],
            [new SWPointDecoder(SEC2CurveFactory::secp192r1())],
            [new TwEDPointDecoder(BernsteinCurveFactory::edwards25519())],
        ];
    }

    /**
     * @dataProvider pointDecoders
     */
    public function testFromCoordinatesCreatesPoint(PointDecoderInterface $decoder): void
    {
        $expectedPoint = $decoder->getCurve()->getG();
        $actualPoint = $decoder->fromCoordinates($expectedPoint->x, $expectedPoint->y);

        $this->assertTrue($expectedPoint->equals($actualPoint));
    }

    /**
     * @dataProvider pointDecoders
     */
    public function testFromXCoordinatesCreatesPoint(PointDecoderInterface $decoder): void
    {
        $expectedPoint = $decoder->getCurve()->getG();
        $isEvenY = gmp_cmp(gmp_mod($expectedPoint->y, 2), 0) === 0;
        $actualPoint = $decoder->fromXCoordinate($expectedPoint->x, $isEvenY);

        $this->assertTrue($expectedPoint->equals($actualPoint));
    }

    /**
     * @dataProvider pointDecoders
     */
    public function testFromXCoordinatesRespectsSignBit(PointDecoderInterface $decoder): void
    {
        $expectedPoint = $decoder->getCurve()->getG();
        $isEvenY = gmp_cmp(gmp_mod($expectedPoint->y, 2), 0) === 0;

        $actualPoint = $decoder->fromXCoordinate($expectedPoint->x, $isEvenY);
        $this->assertTrue($expectedPoint->equals($actualPoint));

        $actualNegatedPoint = $decoder->fromXCoordinate($expectedPoint->x, !$isEvenY);
        $this->assertFalse($expectedPoint->equals($actualNegatedPoint));

        $actualSomePoint = $decoder->fromXCoordinate($expectedPoint->x);
        $this->assertTrue($actualPoint->equals($actualSomePoint) || $actualNegatedPoint->equals($actualSomePoint));
    }

    public function testFromYCoordinatesCreatesPoint(): void
    {
        $decoder = new TwEDPointDecoder(BernsteinCurveFactory::edwards25519());

        $expectedPoint = $decoder->getCurve()->getG();
        $isEvenX = gmp_cmp(gmp_mod($expectedPoint->x, 2), 0) === 0;
        $actualPoint = $decoder->fromYCoordinate($expectedPoint->y, $isEvenX);

        $this->assertTrue($expectedPoint->equals($actualPoint));
    }

    public function testFromYCoordinatesRespectsSignBit(): void
    {
        $decoder = new TwEDPointDecoder(BernsteinCurveFactory::edwards25519());

        $expectedPoint = $decoder->getCurve()->getG();
        $isEvenX = gmp_cmp(gmp_mod($expectedPoint->x, 2), 0) === 0;

        $actualPoint = $decoder->fromYCoordinate($expectedPoint->y, $isEvenX);
        $this->assertTrue($expectedPoint->equals($actualPoint));

        $actualPoint = $decoder->fromYCoordinate($expectedPoint->y, !$isEvenX);
        $this->assertFalse($expectedPoint->equals($actualPoint));
    }
}

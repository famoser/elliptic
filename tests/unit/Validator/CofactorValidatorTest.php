<?php

namespace Famoser\Elliptic\Tests\Unit\Validator;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\TwED_ANeg1_Math;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\Decoder\RFC7784Decoder;
use Famoser\Elliptic\Serializer\PointDecoder\TwEDPointDecoder;
use Famoser\Elliptic\Validator\CofactorValidator;
use PHPUnit\Framework\TestCase;

class CofactorValidatorTest extends TestCase
{
    public function testBasepointValidates(): void
    {
        $curve = BernsteinCurveFactory::edwards25519();
        $math = new TwED_ANeg1_Math($curve);

        $validator = new CofactorValidator($math);

        $basepoint = $curve->getG();
        $this->assertTrue($validator->isValidPoint($basepoint));
    }

    public static function smallOrderPoints(): array
    {
        // https://monero.stackexchange.com/a/8672
        $generators = [
            'c7176a703d4dd84fba3c0b760d10670f2a2053fa2c39ccc64ec7fd7792ac03fa',
            '0000000000000000000000000000000000000000000000000000000000000000',
            '26e8958fc2b227b045c3f489f2ef98f0d5dfac05d3c63339b13802886d53fc85',
            'ecffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff7f',
            '26e8958fc2b227b045c3f489f2ef98f0d5dfac05d3c63339b13802886d53fc05',
            '0000000000000000000000000000000000000000000000000000000000000080',
            'c7176a703d4dd84fba3c0b760d10670f2a2053fa2c39ccc64ec7fd7792ac037a',
            '0100000000000000000000000000000000000000000000000000000000000000'
        ];

        $deserializer = new RFC7784Decoder();

        $curve = BernsteinCurveFactory::edwards25519();
        $decoder = new TwEDPointDecoder($curve);
        $math = new TwED_ANeg1_Math($curve);

        $smallOrderPoints = [];
        foreach ($generators as $generator) {
            $uCoordinate = $deserializer->decodeUCoordinate($generator, 255);
            // TODO: this is wrong. would expect "fromXCoordinate"
            $smallOrderPoints[] = [$math, $curve, $decoder->fromYCoordinate($uCoordinate, true)];
        }

        return $smallOrderPoints;
    }

    /**
     * @dataProvider smallOrderPoints
     */
    public function testDetectsPointsOfSmallOrder(MathInterface $math, Curve $curve, Point $smallOrderPoint): void
    {
        $validator = new CofactorValidator($math);
        $this->assertTrue($validator->hasSmallOrder($smallOrderPoint));
    }

    /**
     * @dataProvider smallOrderPoints
     */
    public function testDetectsBasepointTorsion(MathInterface $math, Curve $curve, Point $smallOrderPoint): void
    {
        $validator = new CofactorValidator($math);

        if ($math->isInfinity($smallOrderPoint)) {
            $this->markTestSkipped("already infinity, hence will not change basegroup");
        }

        $torsionGroup = $math->add($curve->getG(), $smallOrderPoint);
        $this->assertFalse($validator->isInBasepointTorsion($torsionGroup));

        $randomTorsionGroupElement = $math->mul($torsionGroup, gmp_init(127391823));
        $this->assertFalse($validator->isInBasepointTorsion($randomTorsionGroupElement));
    }
}

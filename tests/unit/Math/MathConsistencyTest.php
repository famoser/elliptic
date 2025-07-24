<?php

namespace Famoser\Elliptic\Tests\Math;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Math\EDMath;
use Famoser\Elliptic\Math\EDUnsafeMath;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\MG_ED_Math;
use Famoser\Elliptic\Math\MG_TwED_Math;
use Famoser\Elliptic\Math\MGUnsafeMath;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\SW_QT_ANeg3_Math;
use Famoser\Elliptic\Math\SWUnsafeMath;
use Famoser\Elliptic\Primitives\Point;
use PHPUnit\Framework\TestCase;

class MathConsistencyTest extends TestCase
{
    public static function maths(): array
    {
        $secpCurves = [
            SEC2CurveFactory::secp192r1(),
            SEC2CurveFactory::secp224r1(),
            SEC2CurveFactory::secp256r1(),
            SEC2CurveFactory::secp384r1(),
            SEC2CurveFactory::secp521r1(),
        ];

        $brainpoolCurves = [
            BrainpoolCurveFactory::p160t1(),
            BrainpoolCurveFactory::p192t1(),
            BrainpoolCurveFactory::p224t1(),
            BrainpoolCurveFactory::p256t1(),
            BrainpoolCurveFactory::p320t1(),
            BrainpoolCurveFactory::p384t1(),
            BrainpoolCurveFactory::p512t1(),
        ];

        $brainpoolTwistedCurves = [
            [BrainpoolCurveFactory::p160r1(), BrainpoolCurveFactory::p160r1TwistToP160t1()],
            [BrainpoolCurveFactory::p192r1(), BrainpoolCurveFactory::p192r1TwistToP192t1()],
            [BrainpoolCurveFactory::p224r1(), BrainpoolCurveFactory::p224r1TwistToP224t1()],
            [BrainpoolCurveFactory::p256r1(), BrainpoolCurveFactory::p256r1TwistToP256t1()],
            [BrainpoolCurveFactory::p320r1(), BrainpoolCurveFactory::p320r1TwistToP320t1()],
            [BrainpoolCurveFactory::p384r1(), BrainpoolCurveFactory::p384r1TwistToP384t1()],
            [BrainpoolCurveFactory::p512r1(), BrainpoolCurveFactory::p512r1TwistToP512t1()],
        ];

        $bernsteinTwistedEDCurves = [
            [BernsteinCurveFactory::curve25519(), BernsteinCurveFactory::curve25519ToEdwards25519(), BernsteinCurveFactory::edwards25519()]
        ];

        $bernsteinEDCurves = [
            [BernsteinCurveFactory::curve448(), BernsteinCurveFactory::curve448ToEdwards(), BernsteinCurveFactory::curve448Edwards()]
        ];

        $edwardsCurves = [
            BernsteinCurveFactory::edwards448(),
            BernsteinCurveFactory::curve448Edwards()
        ];

        $testsets = [];
        foreach (array_merge($secpCurves, $brainpoolCurves) as $curve) {
            $testsets[] = [new SW_ANeg3_Math($curve)];
            $testsets[] = [new SWUnsafeMath($curve)];
        }
        foreach ($brainpoolTwistedCurves as $curveAndTwist) {
            $testsets[] = [new SW_QT_ANeg3_Math(...$curveAndTwist)];
            $testsets[] = [new SWUnsafeMath($curveAndTwist[0])];
        }
        foreach ($bernsteinTwistedEDCurves as $curveAndMapping) {
            $testsets[] = [new MG_TwED_Math(...$curveAndMapping)];
            $testsets[] = [new MGUnsafeMath($curveAndMapping[0])];
        }
        foreach ($bernsteinEDCurves as $curveAndMapping) {
            $testsets[] = [new MG_ED_Math(...$curveAndMapping)];
            $testsets[] = [new MGUnsafeMath($curveAndMapping[0])];
        }
        foreach ($edwardsCurves as $curve) {
            $testsets[] = [new EDMath($curve)];
            $testsets[] = [new EDUnsafeMath($curve)];
        }

        return $testsets;
    }

    /**
     * @dataProvider maths
     */
    public function testAddAndDoubleConsistency(MathInterface $math): void
    {
        $curve = $math->getCurve();

        $addDouble = $math->add($curve->getG(), $curve->getG());
        $doubleDouble = $math->double($curve->getG());
        $this->assertTrue($addDouble->equals($doubleDouble));

        $actual = $math->double($addDouble);
        $expected = $math->add($doubleDouble, $doubleDouble);
        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider maths
     */
    public function testMulGEqualsMul(MathInterface $math): void
    {
        $curve = $math->getCurve();

        for ($i = 1; $i < 5; $i++) {
            $factor = gmp_init($i);
            $expected = $math->mul($curve->getG(), $factor);
            $actual = $math->mulG($factor);
            $this->assertObjectEquals($expected, $actual);
        }
    }

    /**
     * @dataProvider maths
     */
    public function testMulEqualsDoubleAdd(MathInterface $math): void
    {
        $curve = $math->getCurve();

        // (1 + 1) * 2 + 1 = 5
        $onePlusOne = $math->add($curve->getG(), $curve->getG());
        $doubledOnePlusOne = $math->double($onePlusOne);
        $expected = $math->add($doubledOnePlusOne, $curve->getG());

        $factor = gmp_init(5);
        $actual = $math->mul($curve->getG(), $factor);

        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider maths
     */
    public function testMulCycle(MathInterface $math): void
    {
        $curve = $math->getCurve();

        $nhPlusOne = gmp_add(gmp_mul($curve->getN(), $curve->getH()), 1);
        $actual = $math->mul($curve->getG(), $nhPlusOne);
        $this->assertObjectEquals($curve->getG(), $actual);

        $nPlusOne = gmp_add($curve->getN(), 1);
        $actual = $math->mul($curve->getG(), $nPlusOne);
        $this->assertObjectEquals($curve->getG(), $actual);

        $actual = $math->mul($curve->getG(), $curve->getN());
        $this->assertObjectEquals(Point::createInfinity(), $actual);
    }

    /**
     * @dataProvider maths
     */
    public function testInfinity(MathInterface $math): void
    {
        $curve = $math->getCurve();

        $orderMinusOne = gmp_sub($curve->getN(), 1);
        $nMinusOne = $math->mul($curve->getG(), $orderMinusOne);
        $actual = $math->add($nMinusOne, $curve->getG());
        $this->assertObjectEquals($actual, Point::createInfinity());
    }
}

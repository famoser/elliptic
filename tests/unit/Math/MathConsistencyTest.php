<?php

namespace Famoser\Elliptic\Tests\Math;

use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Curves\SEC2CurveFactory;
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
    public static function mathToCompareTo(): array
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

        $bernsteinTwistedCurves = [
            [BernsteinCurveFactory::curve25519(), BernsteinCurveFactory::curve25519ToEdwards25519(), BernsteinCurveFactory::edwards25519()]
        ];

        $bernsteinCurves = [
            [BernsteinCurveFactory::curve448(), BernsteinCurveFactory::curve448ToEdwards(), BernsteinCurveFactory::curve448Edwards()]
        ];

        $testsets = [];
        foreach (array_merge($secpCurves, $brainpoolCurves) as $curve) {
            $testsets[] = [new SW_ANeg3_Math($curve), new SWUnsafeMath($curve)];
        }
        foreach ($brainpoolTwistedCurves as $curveAndTwist) {
            $math = new SW_QT_ANeg3_Math(...$curveAndTwist);
            $testsets[] = [$math, new SWUnsafeMath($math->getCurve())];
        }
        foreach ($bernsteinTwistedCurves as $curveAndMapping) {
            $math = new MG_TwED_Math(...$curveAndMapping);
            $testsets[] = [$math, new MGUnsafeMath($math->getCurve())];
        }
        foreach ($bernsteinCurves as $curveAndMapping) {
            $math = new MG_ED_Math(...$curveAndMapping);
            $testsets[] = [$math, new MGUnsafeMath($math->getCurve())];
        }

        return $testsets;
    }

    /**
     * @dataProvider mathToCompareTo
     */
    public function testAdd(MathInterface $math, MathInterface $groundTruth): void
    {
        $curve = $math->getCurve();

        $expected = $groundTruth->add($curve->getG(), $curve->getG());
        $actual = $math->add($curve->getG(), $curve->getG());

        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider mathToCompareTo
     */
    public function testAddInfinity(MathInterface $math, MathInterface $groundTruth): void
    {
        $this->markTestSkipped("Adding the point at infinity is undefined behavior; and as it turns out not all calculators handle the case equally.");

        /** @phpstan-ignore deadCode.unreachable */
        $curve = $math->getCurve();

        $expected = $groundTruth->add($curve->getG(), Point::createInfinity());
        $actual = $math->add($curve->getG(), Point::createInfinity());

        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider mathToCompareTo
     */
    public function testDouble(MathInterface $math, MathInterface $groundTruth): void
    {
        $curve = $math->getCurve();

        $expected = $groundTruth->double($curve->getG());
        $actual = $math->double($curve->getG());

        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider mathToCompareTo
     */
    public function testDoubleEqualsAddSelf(MathInterface $math): void
    {
        $curve = $math->getCurve();

        $expected = $math->add($curve->getG(), $curve->getG());
        $actual = $math->double($curve->getG());

        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider mathToCompareTo
     */
    public function testMulG(MathInterface $math, MathInterface $groundTruth): void
    {
        $factor = gmp_init(5);
        $expected = $groundTruth->mulG($factor);
        $actual = $math->mulG($factor);

        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider mathToCompareTo
     */
    public function testMulGEqualsMul(MathInterface $math): void
    {
        $curve = $math->getCurve();

        $factor = gmp_init(5);
        $expected = $math->mul($curve->getG(), $factor);
        $actual = $math->mulG($factor);

        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider mathToCompareTo
     */
    public function testMulSameResult(MathInterface $math, MathInterface $groundTruth): void
    {
        $curve = $math->getCurve();

        $factor = gmp_init(5);
        $expected = $groundTruth->mul($curve->getG(), $factor);
        $actual = $math->mul($curve->getG(), $factor);

        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider mathToCompareTo
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
}

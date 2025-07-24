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

class MathComparisonTest extends TestCase
{
    public static function mathWithBaseline(): array
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

        $bernsteinEdCurves = [
            [BernsteinCurveFactory::curve448(), BernsteinCurveFactory::curve448ToEdwards(), BernsteinCurveFactory::curve448Edwards()]
        ];

        $edwardsCurves = [
            BernsteinCurveFactory::edwards448(),
            BernsteinCurveFactory::curve448Edwards()
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
        foreach ($bernsteinEdCurves as $curveAndMapping) {
            $math = new MG_ED_Math(...$curveAndMapping);
            $testsets[] = [$math, new MGUnsafeMath($math->getCurve())];
        }
        foreach ($edwardsCurves as $curve) {
            $testsets[] = [new EDMath($curve), new EDUnsafeMath($curve)];
        }

        return $testsets;
    }

    /**
     * @dataProvider mathWithBaseline
     */
    public function testAdd(MathInterface $math, MathInterface $baseline): void
    {
        $curve = $math->getCurve();

        $expected = $baseline->add($curve->getG(), $curve->getG());
        $actual = $math->add($curve->getG(), $curve->getG());

        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider mathWithBaseline
     */
    public function testDouble(MathInterface $math, MathInterface $baseline): void
    {
        $curve = $math->getCurve();

        $expected = $baseline->double($curve->getG());
        $actual = $math->double($curve->getG());

        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider mathWithBaseline
     */
    public function testMulSameResult(MathInterface $math, MathInterface $baseline): void
    {
        $curve = $math->getCurve();

        $factors = array_map(static fn ($number) => gmp_init($number), [1,2,3,4,5]);
        $factors[] = $curve->getN();
        $factors[] = gmp_sub($curve->getN(), 1);
        $factors[] = gmp_add($curve->getN(), 1);

        foreach ($factors as $factor) {
            $expected = $baseline->mul($curve->getG(), $factor);
            $actual = $math->mul($curve->getG(), $factor);
            $this->assertObjectEquals($expected, $actual);
        }
    }
}

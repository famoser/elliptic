<?php

namespace Famoser\Elliptic\Tests\Math;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Math\EDMath;
use Famoser\Elliptic\Math\EDUnsafeMath;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\MG_ED_Math;
use Famoser\Elliptic\Math\MG_TwED_ANeg1_Math;
use Famoser\Elliptic\Math\MGUnsafeMath;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\SW_QT_ANeg3_Math;
use Famoser\Elliptic\Math\SWUnsafeMath;
use Famoser\Elliptic\Math\TwED_ANeg1_Math;
use Famoser\Elliptic\Math\TwEDUnsafeMath;
use Famoser\Elliptic\Tests\TestUtils\UnresolvedErrorTrait;
use PHPUnit\Framework\TestCase;

class ConsistencyTest extends TestCase
{
    use UnresolvedErrorTrait;

    public static function maths(): array
    {
        $secpCurves = [
            'secp192r1' => SEC2CurveFactory::secp192r1(),
            'secp224r1' => SEC2CurveFactory::secp224r1(),
            'secp256r1' => SEC2CurveFactory::secp256r1(),
            'secp384r1' => SEC2CurveFactory::secp384r1(),
            'secp521r1' => SEC2CurveFactory::secp521r1(),
        ];

        $brainpoolCurves = [
            'p160t1' => BrainpoolCurveFactory::p160t1(),
            'p192t1' => BrainpoolCurveFactory::p192t1(),
            'p224t1' => BrainpoolCurveFactory::p224t1(),
            'p256t1' => BrainpoolCurveFactory::p256t1(),
            'p320t1' => BrainpoolCurveFactory::p320t1(),
            'p384t1' => BrainpoolCurveFactory::p384t1(),
            'p512t1' => BrainpoolCurveFactory::p512t1(),
        ];

        $brainpoolTwistedCurves = [
            'p160r1' => [BrainpoolCurveFactory::p160r1(), BrainpoolCurveFactory::p160r1TwistToP160t1()],
            'p192r1' => [BrainpoolCurveFactory::p192r1(), BrainpoolCurveFactory::p192r1TwistToP192t1()],
            'p224r1' => [BrainpoolCurveFactory::p224r1(), BrainpoolCurveFactory::p224r1TwistToP224t1()],
            'p256r1' => [BrainpoolCurveFactory::p256r1(), BrainpoolCurveFactory::p256r1TwistToP256t1()],
            'p320r1' => [BrainpoolCurveFactory::p320r1(), BrainpoolCurveFactory::p320r1TwistToP320t1()],
            'p384r1' => [BrainpoolCurveFactory::p384r1(), BrainpoolCurveFactory::p384r1TwistToP384t1()],
            'p512r1' => [BrainpoolCurveFactory::p512r1(), BrainpoolCurveFactory::p512r1TwistToP512t1()],
        ];

        $bernsteinTwistedCurves = [
            'curve25519ToEdwards25519' => [BernsteinCurveFactory::curve25519(), BernsteinCurveFactory::curve25519ToEdwards25519(), BernsteinCurveFactory::edwards25519()]
        ];

        $bernsteinEdCurves = [
            'curve448ToEdwards' => [BernsteinCurveFactory::curve448(), BernsteinCurveFactory::curve448ToEdwards(), BernsteinCurveFactory::curve448Edwards()]
        ];

        $twistedEdwardsCurves = [
            'edwards25519' => BernsteinCurveFactory::edwards25519(),
        ];

        $edwardsCurves = [
            'edwards448' => BernsteinCurveFactory::edwards448(),
            'curve448Edwards' => BernsteinCurveFactory::curve448Edwards()
        ];

        $testsets = [];
        foreach (array_merge($secpCurves, $brainpoolCurves) as $name => $curve) {
            $testsets[$name . "_" . SW_ANeg3_Math::class] = [$name, new SW_ANeg3_Math($curve)];
            $testsets[$name . "_" . SWUnsafeMath::class] = [$name, new SWUnsafeMath($curve)];
        }
        foreach ($brainpoolTwistedCurves as $name => $curveAndTwist) {
            $testsets[$name . "_" . SW_QT_ANeg3_Math::class] = [$name, new SW_QT_ANeg3_Math(...$curveAndTwist)];
            $testsets[$name . "_" . SWUnsafeMath::class] = [$name, new SWUnsafeMath($curveAndTwist[0])];
        }
        foreach ($bernsteinTwistedCurves as $name => $curveAndMapping) {
            $testsets[$name . "_" . MG_TwED_ANeg1_Math::class] = [$name, new MG_TwED_ANeg1_Math(...$curveAndMapping)];
            $testsets[$name . "_" . MGUnsafeMath::class] = [$name, new MGUnsafeMath($curveAndMapping[0])];
        }
        foreach ($bernsteinEdCurves as $name => $curveAndMapping) {
            $testsets[$name . "_" . MG_ED_Math::class] = [$name, new MG_ED_Math(...$curveAndMapping)];
            $testsets[$name . "_" . MGUnsafeMath::class] = [$name, new MGUnsafeMath($curveAndMapping[0])];
        }
        foreach ($twistedEdwardsCurves as $name => $curve) {
            $testsets[$name . "_" . TwED_ANeg1_Math::class] = [$name, new TwED_ANeg1_Math($curve)];
            $testsets[$name . "_" . TwEDUnsafeMath::class] = [$name, new TwEDUnsafeMath($curve)];
        }
        foreach ($edwardsCurves as $name => $curve) {
            $testsets[$name . "_" . EDMath::class] = [$name, new EDMath($curve)];
            $testsets[$name . "_" . EDUnsafeMath::class] = [$name, new EDUnsafeMath($curve)];
        }

        return $testsets;
    }

    /**
     * @dataProvider maths
     */
    public function testAddAndDoubleConsistency(string $curveName, MathInterface $math): void
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
    public function testMulGEqualsMul(string $curveName, MathInterface $math): void
    {
        $curve = $math->getCurve();

        $factor = gmp_init(gmp_mul($math->getCurve()->getH(), 25));
        $expected = $math->mul($curve->getG(), $factor);
        $actual = $math->mulG($factor);
        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider maths
     */
    public function testMulEqualsDoubleAdd(string $curveName, MathInterface $math): void
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
    public function testInfinity(string $curveName, MathInterface $math): void
    {
        $curve = $math->getCurve();

        $actual = $math->add($curve->getG(), $math->getInfinity());
        $this->assertObjectEquals($curve->getG(), $actual);

        $actual = $math->add($math->getInfinity(), $curve->getG());
        $this->assertObjectEquals($curve->getG(), $actual);

        $actual = $math->add($math->getInfinity(), $math->getInfinity());
        $this->assertTrue($math->isInfinity($actual));

        $actual = $math->double($math->getInfinity());
        $this->assertTrue($math->isInfinity($actual));

        $actual = $math->mul($math->getInfinity(), gmp_init(5));
        $this->assertTrue($math->isInfinity($actual));
    }

    /**
     * @dataProvider maths
     */
    public function testHConsistency(string $curveName, MathInterface $math): void
    {
        $curve = $math->getCurve();

        $hMul = $math->mul($curve->getG(), $curve->getH());

        $hDouble = $curve->getG();
        $hNumber = (int)gmp_strval($curve->getH());
        $hLog = log($hNumber, 2);
        $this->assertEquals(2 ** $hLog, $hNumber); // sanity check: log2 well-defined
        for ($i = 0; $i < $hLog; ++$i) {
            $hDouble = $math->double($hDouble);
        }

        $hAdd = $curve->getG();
        for ($i = 0; gmp_cmp($i, gmp_sub($curve->getH(), 1)) < 0; ++$i) {
            $hAdd = $math->add($hAdd, $curve->getG());
        }

        $this->assertObjectEquals($hMul, $hDouble);
        $this->assertObjectEquals($hAdd, $hDouble);
    }
}

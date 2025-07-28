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

class ComparisonTest extends TestCase
{
    use UnresolvedErrorTrait;

    public static function mathWithBaseline(): array
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
            $testsets[$name] = [$name, new SW_ANeg3_Math($curve), new SWUnsafeMath($curve)];
        }
        foreach ($brainpoolTwistedCurves as $name => $curveAndTwist) {
            $math = new SW_QT_ANeg3_Math(...$curveAndTwist);
            $testsets[$name] = [$name, $math, new SWUnsafeMath($math->getCurve())];
        }
        foreach ($bernsteinTwistedCurves as $name => $curveAndMapping) {
            $math = new MG_TwED_ANeg1_Math(...$curveAndMapping);
            $testsets[$name] = [$name, $math, new MGUnsafeMath($math->getCurve())];
        }
        foreach ($bernsteinEdCurves as $name => $curveAndMapping) {
            $math = new MG_ED_Math(...$curveAndMapping);
            $testsets[$name] = [$name, $math, new MGUnsafeMath($math->getCurve())];
        }
        foreach ($twistedEdwardsCurves as $name => $curve) {
            $testsets[$name] = [$name, new TwED_ANeg1_Math($curve), new TwEDUnsafeMath($curve)];
        }
        foreach ($edwardsCurves as $name => $curve) {
            $testsets[$name] = [$name, new EDMath($curve), new EDUnsafeMath($curve)];
        }

        return $testsets;
    }

    /**
     * @dataProvider mathWithBaseline
     */
    public function testAdd(string $curveName, MathInterface $math, MathInterface $baseline): void
    {
        $curve = $math->getCurve();

        $expected = $curve->getG();
        $actual = $curve->getG();
        for ($i = 0; gmp_cmp($i, $math->getCurve()->getH()) < 0; ++$i) {
            $expected = $baseline->add($expected, $curve->getG());
            $actual = $math->add($actual, $curve->getG());
        }

        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider mathWithBaseline
     */
    public function testAddG2(string $curveName, MathInterface $math, MathInterface $baseline): void
    {
        $curve = $math->getCurve();

        $G2 = $baseline->double($curve->getG());
        $expected = $curve->getG();
        $actual = $curve->getG();
        for ($i = 0; gmp_cmp($i, $math->getCurve()->getH()) < 0; ++$i) {
            $expected = $baseline->add($expected, $G2);
            $actual = $math->add($actual, $G2);
        }

        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider mathWithBaseline
     */
    public function testDouble(string $curveName, MathInterface $math, MathInterface $baseline): void
    {
        $this->skipUnresolvedError(__CLASS__, __FUNCTION__, $curveName);

        $curve = $math->getCurve();

        $expected = $curve->getG();
        $actual = $curve->getG();
        $hNumber = (int)gmp_strval($math->getCurve()->getH());
        $hLog = log($hNumber, 2);
        $this->assertEquals(2 ** $hLog, $hNumber); // sanity check: log2 well-defined
        for ($i = 0; $i < $hLog; ++$i) {
            $expected = $baseline->double($expected);
            $actual = $math->double($actual);
        }
        $expectedAdd = $curve->getG();
        $actualAdd = $curve->getG();
        for ($i = 0; $i < $hNumber - 1; ++$i) {
            $expectedAdd = $baseline->add($expectedAdd, $curve->getG());
            $actualAdd = $math->add($actualAdd, $curve->getG());
        }

        $this->assertObjectEquals($expected, $actual);
    }
}

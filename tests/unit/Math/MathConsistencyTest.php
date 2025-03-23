<?php

namespace Famoser\Elliptic\Tests\Math;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\UnsafePrimeCurveMath;
use Famoser\Elliptic\Primitives\Point;
use PHPUnit\Framework\TestCase;

class MathConsistencyTest extends TestCase
{
    public static function mathToCompareTo(): array
    {
        return [
            [new SW_ANeg3_Math(SEC2CurveFactory::secp192r1())],
            [new SW_ANeg3_Math(SEC2CurveFactory::secp224r1())],
            [new SW_ANeg3_Math(SEC2CurveFactory::secp256r1())],
            [new SW_ANeg3_Math(SEC2CurveFactory::secp384r1())],
            [new SW_ANeg3_Math(SEC2CurveFactory::secp521r1())]
        ];
    }

    /**
     * @dataProvider mathToCompareTo
     */
    public function testAdd(MathInterface $math): void
    {
        $curve = $math->getCurve();
        $groundTruth = new UnsafePrimeCurveMath($curve);

        $expected = $groundTruth->add($curve->getG(), $curve->getG());
        $actual = $math->add($curve->getG(), $curve->getG());

        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider mathToCompareTo
     */
    public function testAddInfinity(MathInterface $math): void
    {
        $this->markTestSkipped("Adding the point at infinity is undefined behavior; and as it turns out not all calculators handle the case equally.");

        $curve = $math->getCurve();
        $groundTruth = new UnsafePrimeCurveMath($curve);

        $expected = $groundTruth->add($curve->getG(), Point::createInfinity());
        $actual = $math->add($curve->getG(), Point::createInfinity());

        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider mathToCompareTo
     */
    public function testDouble(MathInterface $math): void
    {
        $curve = $math->getCurve();
        $groundTruth = new UnsafePrimeCurveMath($curve);

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
    public function testMulG(MathInterface $math): void
    {
        $curve = $math->getCurve();
        $groundTruth = new UnsafePrimeCurveMath($curve);

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
    public function testMulSameResult(MathInterface $math): void
    {
        $curve = $math->getCurve();
        $groundTruth = new UnsafePrimeCurveMath($curve);

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

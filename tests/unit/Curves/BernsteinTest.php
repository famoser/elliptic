<?php

namespace Famoser\Elliptic\Tests\Curves;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\MG_ED_Math;
use Famoser\Elliptic\Math\MG_TwED_ANeg1_Math;
use Famoser\Elliptic\Primitives\BirationalMap;
use Famoser\Elliptic\Primitives\Curve;
use PHPUnit\Framework\TestCase;

class BernsteinTest extends TestCase
{
    public function testEvaluatedParametersCurve25519(): void
    {
        $curve = BernsteinCurveFactory::curve25519();

        $prime = gmp_sub(gmp_pow(2, 255), 19);
        $this->assertEquals(0, gmp_cmp($prime, $curve->getP()));

        $order = gmp_add(gmp_pow(2, 252), gmp_init('14def9dea2f79cd65812631a5cf5d3ed', 16));
        $this->assertEquals(0, gmp_cmp($order, $curve->getN()));
    }

    public function testEvaluatedParametersEdwards25519(): void
    {
        $curve = BernsteinCurveFactory::edwards25519();

        $prime = gmp_sub(gmp_pow(2, 255), 19);
        $this->assertEquals(0, gmp_cmp($prime, $curve->getP()));

        $order = gmp_add(gmp_pow(2, 252), gmp_init('14def9dea2f79cd65812631a5cf5d3ed', 16));
        $this->assertEquals(0, gmp_cmp($order, $curve->getN()));

        $aNeg1 = gmp_mod(-1, $curve->getP());
        $this->assertEquals(0, gmp_cmp($aNeg1, $curve->getA()));
    }

    public function testEvaluatedParametersCurve448(): void
    {
        $curve = BernsteinCurveFactory::curve448();

        $prime = gmp_sub(gmp_sub(gmp_pow(2, 448), gmp_pow(2, 224)), 1);
        $this->assertEquals(0, gmp_cmp($prime, $curve->getP()));

        $order = gmp_sub(gmp_pow(2, 446), gmp_init('8335dc163bb124b65129c96fde933d8d723a70aadc873d6d54a7bb0d', 16));
        $this->assertEquals(0, gmp_cmp($order, $curve->getN()));
    }

    public function testEvaluatedParametersCurve448Edwards(): void
    {
        $curve = BernsteinCurveFactory::curve448Edwards();

        $prime = gmp_sub(gmp_sub(gmp_pow(2, 448), gmp_pow(2, 224)), 1);
        $this->assertEquals(0, gmp_cmp($prime, $curve->getP()));

        $order = gmp_sub(gmp_pow(2, 446), gmp_init('8335dc163bb124b65129c96fde933d8d723a70aadc873d6d54a7bb0d', 16));
        $this->assertEquals(0, gmp_cmp($order, $curve->getN()));
    }

    public static function provideBirationalMaps(): \Generator
    {
        $curve = BernsteinCurveFactory::curve25519();
        $mapping = BernsteinCurveFactory::curve25519ToEdwards25519();
        $targetCurve = BernsteinCurveFactory::edwards25519();
        $math = new MG_TwED_ANeg1_Math($curve, $mapping, $targetCurve);
        yield [$math, $curve, $mapping, $targetCurve];

        $curve = BernsteinCurveFactory::curve448();
        $mapping = BernsteinCurveFactory::curve448ToEdwards();
        $targetCurve = BernsteinCurveFactory::curve448Edwards();
        $math = new MG_ED_Math($curve, $mapping, $targetCurve);
        yield [$math, $curve, $mapping, $targetCurve];
    }

    /**
     * @dataProvider provideBirationalMaps
     */
    public function testBirationalMappings(MathInterface $math, Curve $curve, BirationalMap $mapping, Curve $targetCurve): void
    {
        $actualG = $mapping->map($math, $curve->getG());
        $this->assertTrue($targetCurve->getG()->equals($actualG));

        $actualG = $mapping->reverse($math, $targetCurve->getG());
        $this->assertTrue($curve->getG()->equals($actualG));

        $actualInfinity = $mapping->map($math, $math->getInfinity());
        $this->assertTrue($math->isInfinity($actualInfinity));

        $actualInfinity = $mapping->reverse($math, $math->getInfinity());
        $this->assertTrue($math->isInfinity($actualInfinity));
    }
}

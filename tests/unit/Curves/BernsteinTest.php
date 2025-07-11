<?php

namespace Famoser\Elliptic\Tests\Curves;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
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
        $this->assertEquals($aNeg1, $curve->getA());
    }

    public function testBirationalMappingOf25519(): void
    {
        $curve = BernsteinCurveFactory::curve25519();
        $mapping = BernsteinCurveFactory::curve25519ToEdwards25519();
        $targetCurve = BernsteinCurveFactory::edwards25519();

        $actualG = $mapping->map($curve->getG());
        $this->assertEquals($targetCurve->getG(), $actualG);

        $actualG = $mapping->reverse($targetCurve->getG());
        $this->assertEquals($curve->getG(), $actualG);
    }

    public function testEvaluatedParametersCurve448(): void
    {
        $curve = BernsteinCurveFactory::curve448();

        $prime = gmp_sub(gmp_sub(gmp_pow(2, 448), gmp_pow(2, 224)), 1);
        $this->assertEquals(0, gmp_cmp($prime, $curve->getP()));

        $order = gmp_sub(gmp_pow(2, 446), gmp_init('8335dc163bb124b65129c96fde933d8d723a70aadc873d6d54a7bb0d', 16));
        $this->assertEquals(0, gmp_cmp($order, $curve->getN()));
    }
}

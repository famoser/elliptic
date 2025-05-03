<?php

namespace Famoser\Elliptic\Tests\Curves;

use Famoser\Elliptic\Curves\MontgomeryCurveFactory;
use PHPUnit\Framework\TestCase;

class MontgomeryTest extends TestCase
{
    public function testEvaluatedParametersCurve25519(): void
    {
        $curve = MontgomeryCurveFactory::curve25519();

        $prime = gmp_sub(gmp_pow(2, 255), 19);
        $this->assertEquals(0, gmp_cmp($prime, $curve->getP()));

        $order = gmp_add(gmp_pow(2, 252), gmp_init('14def9dea2f79cd65812631a5cf5d3ed', 16));
        $this->assertEquals(0, gmp_cmp($order, $curve->getN()));
    }

    public function testEvaluatedParametersCurve448(): void
    {
        $curve = MontgomeryCurveFactory::curve448();

        $prime = gmp_sub(gmp_sub(gmp_pow(2, 448), gmp_pow(2, 224)), 1);
        $this->assertEquals(0, gmp_cmp($prime, $curve->getP()));

        $order = gmp_sub(gmp_pow(2, 446), gmp_init('8335dc163bb124b65129c96fde933d8d723a70aadc873d6d54a7bb0d', 16));
        $this->assertEquals(0, gmp_cmp($order, $curve->getN()));
    }
}

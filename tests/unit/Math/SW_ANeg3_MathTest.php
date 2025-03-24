<?php

namespace Famoser\Elliptic\Tests\Math;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Tests\TestUtils\CurveBuilder;
use PHPUnit\Framework\TestCase;

class SW_ANeg3_MathTest extends TestCase
{
    public static function invalidCurves(): array
    {
        $curve = SEC2CurveFactory::secp384r1();

        $aPlusOne = gmp_add($curve->getA(), 1);
        return [
            // wrong because not short weierstrass
            [(new CurveBuilder($curve))->withType(CurveType::Montgomery)->build()],
            // wrong because a not -3
            [(new CurveBuilder($curve))->withA($aPlusOne)->build()],
            // wrong, failures of above combined
            [(new CurveBuilder($curve))->withType(CurveType::Montgomery)->withA($aPlusOne)->build()],
        ];
    }

    /**
     * @dataProvider invalidCurves
     */
    public function testCannotInstantiateInvalidCurves(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new SW_ANeg3_Math($curve);
    }
}

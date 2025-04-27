<?php

namespace Famoser\Elliptic\Tests\Math;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\SWUnsafeMath;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Tests\TestUtils\CurveBuilder;
use PHPUnit\Framework\TestCase;

class SWUnsafeMathTest extends TestCase
{
    public static function invalidCurves(): array
    {
        $curve = SEC2CurveFactory::secp384r1();

        return [
            // wrong because not short weierstrass
            [(new CurveBuilder($curve))->withType(CurveType::Montgomery)->build()]
        ];
    }

    /**
     * @dataProvider invalidCurves
     */
    public function testCannotInstantiateInvalidCurves(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new SWUnsafeMath($curve);
    }
}

<?php

namespace Famoser\Elliptic\Tests\Math;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\UnsafePrimeCurveMath;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;
use PHPUnit\Framework\TestCase;

class SW_ANeg3_MathTest extends TestCase
{
    public static function invalidCurves(): array
    {
        $curve = SEC2CurveFactory::secp384r1();

        return [
            // wrong because not short weierstrass
            [new Curve(CurveType::Montgomery, $curve->getP(), $curve->getA(), $curve->getB(), $curve->getG(), $curve->getN(), $curve->getH())],
            // wrong because a not -3
            [new Curve(CurveType::ShortWeierstrass, $curve->getP(), gmp_add($curve->getA(), 1), $curve->getB(), $curve->getG(), $curve->getN(), $curve->getH())],
            // wrong, failures of above combined
            [new Curve(CurveType::Montgomery, $curve->getP(), gmp_add($curve->getA(), 1), $curve->getB(), $curve->getG(), $curve->getN(), $curve->getH())],
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

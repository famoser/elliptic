<?php

namespace Famoser\Elliptic\Tests\Math;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Math\MGUnsafeMath;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Tests\TestUtils\CurveBuilder;
use PHPUnit\Framework\TestCase;

class MGUnsafeMathTest extends TestCase
{
    public static function invalidCurves(): array
    {
        $curve = BernsteinCurveFactory::curve25519();

        return [
            // wrong because not montgomery
            [(new CurveBuilder($curve))->withType(CurveType::ShortWeierstrass)->build()],
        ];
    }

    /**
     * @dataProvider invalidCurves
     */
    public function testCannotInstantiateInvalidCurves(Curve $curve): void
    {
        $this->expectException(\AssertionError::class);

        new MGUnsafeMath($curve);
    }
}

<?php

namespace Famoser\Elliptic\Tests\Unit\Math\Calculator;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Math\Calculator\AbstractCalculator;
use Famoser\Elliptic\Math\Calculator\EDCalculator;
use Famoser\Elliptic\Math\Calculator\EDUnsafeCalculator;
use Famoser\Elliptic\Math\Calculator\MGUnsafeCalculator;
use Famoser\Elliptic\Math\Calculator\MGXCalculator;
use Famoser\Elliptic\Math\Calculator\SW_ANeg3_Calculator;
use Famoser\Elliptic\Math\Calculator\SWUnsafeCalculator;
use Famoser\Elliptic\Math\Calculator\TwED_ANeg1_Calculator;
use Famoser\Elliptic\Math\Calculator\TwEDUnsafeCalculator;
use Famoser\Elliptic\Math\SWUnsafeMath;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Tests\Unit\Math\Traits\AssertGmpFields;
use PHPUnit\Framework\TestCase;

class SwapTest extends TestCase
{
    use AssertGmpFields;

    public static function calculators(): array
    {
        $testsets = [];
        $testsets[EDCalculator::class] = [EDCalculator::class, BernsteinCurveFactory::edwards448()]; // Projective coordinates
        $testsets[SW_ANeg3_Calculator::class] = [SW_ANeg3_Calculator::class, SEC2CurveFactory::secp192r1()]; // Jacobi coordinates
        $testsets[SWUnsafeMath::class] = [SWUnsafeCalculator::class, SEC2CurveFactory::secp192r1()]; // Point coordinates
        $testsets[TwED_ANeg1_Calculator::class] = [TwED_ANeg1_Calculator::class, BernsteinCurveFactory::edwards25519()]; // Extended coordinates
        $testsets[TwEDUnsafeCalculator::class] = [TwEDUnsafeCalculator::class, BernsteinCurveFactory::edwards25519()]; // Point 01 coordinates

        return $testsets;
    }

    /**
     * @dataProvider calculators
     */
    public function testInfinity(string $class, Curve $curve): void
    {
        $calculator = new $class($curve);

        if (method_exists($calculator, 'getInfinity') && method_exists($calculator, 'conditionalSwap')) {
            $basepoint = method_exists($calculator, 'affineToNative') ? $calculator->affineToNative($curve->getG()) : $curve->getG();
            $infinity = $calculator->getInfinity();

            $sInfinity = $calculator->getInfinity();
            $sBasepoint = clone $basepoint;
            $calculator->conditionalSwap($sBasepoint, $sInfinity, 0);
            $this->assertGMPFieldsEqual($infinity, $sInfinity);
            $this->assertGMPFieldsEqual($basepoint, $sBasepoint);

            $calculator->conditionalSwap($sBasepoint, $sInfinity, 1);
            $this->assertGMPFieldsEqual($infinity, $sBasepoint);
            $this->assertGMPFieldsEqual($basepoint, $sInfinity);
        } else {
            $this->fail("No conditional swap implemented.");
        }
    }
}

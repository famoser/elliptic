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
use PHPUnit\Framework\TestCase;

class ConsistencyTest extends TestCase
{
    public static function calculators(): array
    {
        $testsets = [];
        $testsets[EDCalculator::class] = [new EDCalculator(BernsteinCurveFactory::edwards448())];
        $testsets[EDUnsafeCalculator::class] = [new EDUnsafeCalculator(BernsteinCurveFactory::edwards448())];
        $testsets[MGUnsafeCalculator::class] = [new MGUnsafeCalculator(BernsteinCurveFactory::curve25519())];
        $testsets[MGXCalculator::class] = [new MGXCalculator(BernsteinCurveFactory::curve25519())];
        $testsets[SW_ANeg3_Calculator::class] = [new SW_ANeg3_Calculator(SEC2CurveFactory::secp192r1())];
        $testsets[SWUnsafeMath::class] = [new SWUnsafeCalculator(SEC2CurveFactory::secp192r1())];
        $testsets[TwED_ANeg1_Calculator::class] = [new TwED_ANeg1_Calculator(BernsteinCurveFactory::edwards25519())];
        $testsets[TwEDUnsafeCalculator::class] = [new TwEDUnsafeCalculator(BernsteinCurveFactory::edwards25519())];

        return $testsets;
    }

    /**
     * @dataProvider calculators
     */
    public function testInfinity(AbstractCalculator $calculator): void
    {
        if (method_exists($calculator, 'getInfinity') && method_exists($calculator, 'isInfinity')) {
            $actual = $calculator->getInfinity();
            if (method_exists($calculator, 'nativeToAffine') && method_exists($calculator, 'affineToNative')) {
                $infinity = $actual;
                $native = $calculator->nativeToAffine($actual);
                $actual = $calculator->affineToNative($native);
                $this->assertTrue($infinity->equals($actual));
            }

            $this->assertTrue($calculator->isInfinity($actual));
        } else {
            $this->markTestSkipped("No infinity handling.");
        }
    }
}

<?php

namespace Famoser\Elliptic\Tests\Unit\Math\Calculator;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Math\Calculator\AbstractCalculator;
use Famoser\Elliptic\Math\Calculator\EDCalculator;
use Famoser\Elliptic\Math\Calculator\SW_ANeg3_Calculator;
use Famoser\Elliptic\Math\Calculator\TwED_ANeg1_Calculator;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Tests\Unit\Math\Traits\AssertGmpFields;
use PHPUnit\Framework\TestCase;

class MultiplicatorTest extends TestCase
{
    use AssertGmpFields;

    public static function calculators(): array
    {
        $testsets = [];
        $testsets[SW_ANeg3_Calculator::class] = [SW_ANeg3_Calculator::class, SEC2CurveFactory::secp192r1()]; // Cofactor 1
        $testsets[EDCalculator::class] = [EDCalculator::class, BernsteinCurveFactory::edwards448()]; // Cofactor 4
        $testsets[TwED_ANeg1_Calculator::class] = [TwED_ANeg1_Calculator::class, BernsteinCurveFactory::edwards25519()]; // Cofactor 8

        return $testsets;
    }

    /**
     * @dataProvider calculators
     */
    public function testCofactor(string $class, Curve $curve): void
    {
        /** @var AbstractCalculator<mixed> $calculator */
        $calculator = new $class($curve);

        $basepoint = method_exists($calculator, 'affineToNative') ? $calculator->affineToNative($curve->getG()) : $curve->getG();
        $expected = $calculator->mul($basepoint, $curve->getH());
        $actual = $calculator->mulH($basepoint);

        $expectedRes = method_exists($calculator, 'nativeToAffine') ? $calculator->nativeToAffine($expected) : $expected;
        $actualRes = method_exists($calculator, 'nativeToAffine') ? $calculator->nativeToAffine($actual) : $actual;

        $this->assertGMPFieldsEqual($expectedRes, $actualRes);
    }
}

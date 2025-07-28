<?php

namespace Famoser\Elliptic\Integration\ExpensiveMath;


use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Math\EDMath;
use Famoser\Elliptic\Math\EDUnsafeMath;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\MG_ED_Math;
use Famoser\Elliptic\Math\MG_TwED_ANeg1_Math;
use Famoser\Elliptic\Math\MGUnsafeMath;
use Famoser\Elliptic\Math\SW_ANeg3_Math;
use Famoser\Elliptic\Math\SW_QT_ANeg3_Math;
use Famoser\Elliptic\Math\SWUnsafeMath;
use Famoser\Elliptic\Math\TwED_ANeg1_Math;
use Famoser\Elliptic\Math\TwEDUnsafeMath;
use Famoser\Elliptic\Tests\TestUtils\UnresolvedErrorTrait;
use PHPUnit\Framework\TestCase;

class ComparisonTest extends \Famoser\Elliptic\Tests\Math\ComparisonTest
{
    /**
     * @dataProvider mathWithBaseline
     */
    public function testMulSameResult(string $curveName, MathInterface $math, MathInterface $baseline): void
    {
        $this->skipUnresolvedError(__CLASS__, __FUNCTION__, $math::class, $curveName);

        $curve = $math->getCurve();

        $factors = array_map(static fn($number) => gmp_mul($number, $curve->getH()), [0, 1, 2, 3]);
        $order = gmp_mul($curve->getN(), $curve->getH());
        $factors[4] = $order;
        $factors[5] = gmp_sub($order, $curve->getH());
        $factors[6] = gmp_add($order, $curve->getH());
        $factors[7] = gmp_sub($order, gmp_mul(2312312, $curve->getH())); // random number close to group order

        foreach ($factors as $i => $factor) {
            $expected = $baseline->mul($curve->getG(), $factor);
            $actual = $math->mul($curve->getG(), $factor);
            if (gmp_cmp($factor, 0) === 0 || gmp_cmp($factor, $order) === 0) {
                $this->assertTrue($baseline->isInfinity($expected));
                $this->assertTrue($math->isInfinity($actual));
            } else {
                $this->assertObjectEquals($expected, $actual, 'equals', "Failed for factor " . $i . " (" . gmp_strval($factor, 16) . ")");
            }
        }
    }
}

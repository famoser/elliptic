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

class ConsistencyTest extends \Famoser\Elliptic\Tests\Math\ConsistencyTest
{
    /**
     * @dataProvider maths
     */
    public function testMulCycle(string $curveName, MathInterface $math): void
    {
        $this->skipUnresolvedError(__CLASS__, __FUNCTION__, $math::class, $curveName);

        $curve = $math->getCurve();

        $bigOrder = gmp_mul($curve->getN(), $curve->getH());
        $actual = $math->mul($curve->getG(), $bigOrder);
        $this->assertTrue($math->isInfinity($actual));

        $orderPlusH = gmp_add(gmp_mul($curve->getN(), $curve->getH()), $curve->getH());
        $actual = $math->mul($curve->getG(), $orderPlusH);
        $Gh = $math->mul($curve->getG(), $curve->getH());
        $this->assertObjectEquals($Gh, $actual);

        $orderMinusH = gmp_sub(gmp_mul($curve->getN(), $curve->getH()), $curve->getH());
        $actual = $math->add($math->mul($curve->getG(), $orderMinusH), $Gh);
        $this->assertTrue($math->isInfinity($actual));
    }
}

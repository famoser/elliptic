<?php

namespace Famoser\Elliptic\Tests\Integration\ExpensiveMath;

use Famoser\Elliptic\Math\MathInterface;

class ConsistencyTest extends \Famoser\Elliptic\Tests\Unit\Math\ConsistencyTest
{
    use UnresolvedErrorTrait;

    /**
     * @dataProvider maths
     */
    public function testAddAndDoubleConsistency(string $curveName, MathInterface $math): void
    {
        $curve = $math->getCurve();

        $addDouble = $math->add($curve->getG(), $curve->getG());
        $doubleDouble = $math->double($curve->getG());
        $this->assertTrue($addDouble->equals($doubleDouble));

        $actual = $math->double($addDouble);
        $expected = $math->add($doubleDouble, $doubleDouble);
        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider maths
     */
    public function testMulGEqualsMul(string $curveName, MathInterface $math): void
    {
        $curve = $math->getCurve();

        $factor = gmp_mul($math->getCurve()->getH(), 25);
        $expected = $math->mul($curve->getG(), $factor);
        $actual = $math->mulG($factor);
        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider maths
     */
    public function testMulEqualsDoubleAdd(string $curveName, MathInterface $math): void
    {
        $curve = $math->getCurve();

        // (1 + 1) * 2 + 1 = 5
        $onePlusOne = $math->add($curve->getG(), $curve->getG());
        $doubledOnePlusOne = $math->double($onePlusOne);
        $expected = $math->add($doubledOnePlusOne, $curve->getG());

        $factor = gmp_init(5);
        $actual = $math->mul($curve->getG(), $factor);

        $this->assertObjectEquals($expected, $actual);
    }

    /**
     * @dataProvider maths
     */
    public function testInfinity(string $curveName, MathInterface $math): void
    {
        $curve = $math->getCurve();

        $actual = $math->add($curve->getG(), $math->getInfinity());
        $this->assertObjectEquals($curve->getG(), $actual);

        $actual = $math->add($math->getInfinity(), $curve->getG());
        $this->assertObjectEquals($curve->getG(), $actual);

        $actual = $math->add($math->getInfinity(), $math->getInfinity());
        $this->assertTrue($math->isInfinity($actual));

        $actual = $math->double($math->getInfinity());
        $this->assertTrue($math->isInfinity($actual));

        $actual = $math->mul($math->getInfinity(), gmp_init(5));
        $this->assertTrue($math->isInfinity($actual));
    }

    /**
     * @dataProvider maths
     */
    public function testHConsistency(string $curveName, MathInterface $math): void
    {
        $curve = $math->getCurve();

        $hMul = $math->mul($curve->getG(), $curve->getH());

        $hDouble = $curve->getG();
        $hNumber = (int)gmp_strval($curve->getH());
        $hLog = log($hNumber, 2);
        $this->assertEquals(2 ** $hLog, $hNumber); // sanity check: log2 well-defined
        for ($i = 0; $i < $hLog; ++$i) {
            $hDouble = $math->double($hDouble);
        }

        $hAdd = $curve->getG();
        for ($i = 0; gmp_cmp($i, gmp_sub($curve->getH(), 1)) < 0; ++$i) {
            $hAdd = $math->add($hAdd, $curve->getG());
        }

        $this->assertObjectEquals($hMul, $hDouble);
        $this->assertObjectEquals($hAdd, $hDouble);
    }

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

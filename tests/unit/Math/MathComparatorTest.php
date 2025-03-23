<?php

namespace Famoser\Elliptic\Tests\Math;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\UnsafeMath;
use Famoser\Elliptic\Math\UnsafeShortWeierstrassANeg3Math;
use PHPUnit\Framework\TestCase;

class MathComparatorTest extends TestCase
{
    public static function calculatorsToCompareTo(): array
    {
        return [
            [new UnsafeShortWeierstrassANeg3Math(SEC2CurveFactory::secp256r1())]
        ];
    }

    /**
     * @dataProvider calculatorsToCompareTo
     */
    public function testAddSameResult(MathInterface $math): void
    {
        $curve = $math->getCurve();
        $groundTruth = new UnsafeMath($curve);

        $expected = $groundTruth->add($curve->getG(), $curve->getG());
        $actual = $math->add($curve->getG(), $curve->getG());

        $this->assertEquals(0, gmp_cmp($expected->x, $actual->x));
        $this->assertEquals(0, gmp_cmp($expected->y, $actual->y));
    }

    /**
     * @dataProvider calculatorsToCompareTo
     */
    public function testDoubleSameResult(MathInterface $math): void
    {
        $curve = $math->getCurve();
        $groundTruth = new UnsafeMath($curve);

        $expected = $groundTruth->double($curve->getG());
        $actual = $math->double($curve->getG());

        $this->assertEquals(0, gmp_cmp($expected->x, $actual->x));
        $this->assertEquals(0, gmp_cmp($expected->y, $actual->y));
    }

    /**
     * @dataProvider calculatorsToCompareTo
     */
    public function testMulGSameResult(MathInterface $math): void
    {
        $curve = $math->getCurve();
        $groundTruth = new UnsafeMath($curve);

        $factor = gmp_init(5);
        $expected = $groundTruth->mulG($factor);
        $actual = $math->mulG($factor);

        $this->assertEquals(0, gmp_cmp($expected->x, $actual->x));
    }

    /**
     * @dataProvider calculatorsToCompareTo
     */
    public function testMulSameResult(MathInterface $math): void
    {
        $curve = $math->getCurve();
        $groundTruth = new UnsafeMath($curve);

        $factor = gmp_init(5);
        $expected = $groundTruth->mul($curve->getG(), $factor);
        $actual = $math->mul($curve->getG(), $factor);

        $this->assertEquals(0, gmp_cmp($expected->x, $actual->x));
    }
}

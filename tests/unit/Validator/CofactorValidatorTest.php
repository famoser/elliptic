<?php

namespace Famoser\Elliptic\Tests\Unit\Validator;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Math\MathInterface;
use Famoser\Elliptic\Math\TwED_ANeg1_Math;
use Famoser\Elliptic\Math\TwEDUnsafeMath;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Validator\CofactorValidator;
use PHPUnit\Framework\TestCase;

class CofactorValidatorTest extends TestCase
{
    public function testBasepointValidates(): void
    {
        $curve = BernsteinCurveFactory::edwards25519();
        $math = new TwED_ANeg1_Math($curve);

        $validator = new CofactorValidator($math);

        $basepoint = $curve->getG();
        $this->assertTrue($validator->isValidPoint($basepoint));
    }

    public static function edwards25519SmallOrderPoints(): array
    {
        /**
         * following the hint from https://monero.stackexchange.com/a/8672,
         * found by randomly choosing a point, and then multiplying by order N
         * all unique results stored as the generator points (order of the points is therefore undefined)
         */
        $generatorPoints = [
            ['0', '1'],
            ['0', '7fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffec'],
            ['547cdb7fb03e20f4d4b2ff66c2042858d0bce7f952d01b873b11e4d8b5f15f3d', '0'],
            ['2b8324804fc1df0b2b4d00993dfbd7a72f431806ad2fe478c4ee1b274a0ea0b0', '0'],
            ['1fd5b9a006394a28e933993238de4abb5c193c7013e5e238dea14646c545d14a', '5fc536d880238b13933c6d305acdfd5f098eff289f4c345b027b2c28f95e826'],
            ['1fd5b9a006394a28e933993238de4abb5c193c7013e5e238dea14646c545d14a', '7a03ac9277fdc74ec6cc392cfa53202a0f67100d760b3cba4fd84d3d706a17c7'],
            ['602a465ff9c6b5d716cc66cdc721b544a3e6c38fec1a1dc7215eb9b93aba2ea3', '5fc536d880238b13933c6d305acdfd5f098eff289f4c345b027b2c28f95e826'],
            ['602a465ff9c6b5d716cc66cdc721b544a3e6c38fec1a1dc7215eb9b93aba2ea3', '7a03ac9277fdc74ec6cc392cfa53202a0f67100d760b3cba4fd84d3d706a17c7'],
        ];

        $curve = BernsteinCurveFactory::edwards25519();
        $maths = [
            new TwEDUnsafeMath($curve),
            new TwED_ANeg1_Math($curve)
        ];

        $testcases = [];
        foreach ($generatorPoints as [$x, $y]) {
            $smallOrderPoint = new Point(gmp_init($x, 16), gmp_init($y, 16));
            foreach ($maths as $math) {
                $testcases[] = [$math, $curve, $smallOrderPoint];
            }
        }

        return $testcases;
    }

    /**
     * @dataProvider edwards25519SmallOrderPoints
     */
    public function testDetectsPointsOfSmallOrder(MathInterface $math, Curve $curve, Point $smallOrderPoint): void
    {
        $validator = new CofactorValidator($math);
        $this->assertTrue($validator->hasSmallOrder($smallOrderPoint));
    }

    /**
     * @dataProvider edwards25519SmallOrderPoints
     */
    public function testDetectsBasepointTorsion(MathInterface $math, Curve $curve, Point $smallOrderPoint): void
    {
        $validator = new CofactorValidator($math);

        $torsionGroup = $math->add($curve->getG(), $smallOrderPoint);
        if ($torsionGroup->equals($curve->getG())) {
            $this->markTestSkipped("already infinity, hence will not change basegroup");
        }

        $this->assertFalse($validator->isInBasepointTorsion($torsionGroup));

        $randomTorsionGroupElement = $math->mul($torsionGroup, gmp_init(127391823));
        $this->assertFalse($validator->isInBasepointTorsion($randomTorsionGroupElement));
    }
}

<?php

namespace Famoser\Elliptic\Tests\Unit\Math\Calculator;

use Famoser\Elliptic\Curves\SEC2CurveFactory;
use Famoser\Elliptic\Math\Calculator\SW_ANeg3_Calculator;
use PHPUnit\Framework\TestCase;

class SW_ANeg3_CalculatorTest extends TestCase
{
    public function testAffineAddSanity(): void
    {
        $curve = SEC2CurveFactory::secp192r1();
        $calculator = new SW_ANeg3_Calculator($curve);

        $GNative = $calculator->affineToNative($curve->getG());
        $expectedResult = $calculator->add($GNative, $GNative);
        $actualResult = $calculator->addAffine($GNative, $curve->getG());

        $this->assertTrue($expectedResult->equals($actualResult));
    }
}

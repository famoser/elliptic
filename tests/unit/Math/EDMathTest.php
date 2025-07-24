<?php

namespace Famoser\Elliptic\Tests\Math;

use Famoser\Elliptic\Curves\BernsteinCurveFactory;
use Famoser\Elliptic\Math\EDMath;
use Famoser\Elliptic\Math\EDUnsafeMath;
use Famoser\Elliptic\Primitives\Point;
use PHPUnit\Framework\TestCase;

class EDMathTest extends TestCase
{
    public function testSampleValues()
    {
        $curve = BernsteinCurveFactory::curve448Edwards();
        $math = new EDMath($curve);
        $baselineMath = new EDUnsafeMath($curve);

        $actualAdd = $math->add($curve->getG(), $curve->getG());
        $actualDouble = $math->double($curve->getG());
        $baselineAdd = $baselineMath->add($curve->getG(), $curve->getG());
        $baselineDouble = $baselineMath->double($curve->getG());

        $this->assertObjectEquals($baselineAdd, $actualAdd);
        $this->assertObjectEquals($baselineDouble, $actualDouble);
        $this->assertObjectEquals($baselineAdd, $baselineDouble);
    }

    public function testSampleValuesGolidlocks()
    {
        $curve = BernsteinCurveFactory::edwards448();
        $math = new EDMath($curve);

        $actualAdd = $math->add($curve->getG(), $curve->getG());
        $actualDouble = $math->double($curve->getG());
        $expected = new Point(
            gmp_init('209710714663589237570084264541991420589833663592202160838176801982171960997051286469552065490170659385708816452452440655275673121357616', 10),
            gmp_init('603515570432573637134887094808958022419371301976351441963100315034426774344109511210661998660350679225364893651728492312845104034682937', 10),
        );

        $this->assertObjectEquals($expected, $actualDouble);
        $this->assertObjectEquals($expected, $actualAdd);
    }
}

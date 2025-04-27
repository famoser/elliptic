<?php

namespace Famoser\Elliptic\Tests\Curves;

use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Math\Twister\QuadraticTwister;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\QuadraticTwist;
use PHPUnit\Framework\TestCase;

class BrainpoolTest extends TestCase
{
    public static function provideTwistedCurves(): array
    {
        return [
            [BrainpoolCurveFactory::p160r1(), BrainpoolCurveFactory::p160r1TwistToP160t1(), BrainpoolCurveFactory::p160t1()],
            [BrainpoolCurveFactory::p192r1(), BrainpoolCurveFactory::p192r1TwistToP192t1(), BrainpoolCurveFactory::p192t1()],
            [BrainpoolCurveFactory::p224r1(), BrainpoolCurveFactory::p224r1TwistToP224t1(), BrainpoolCurveFactory::p224t1()],
            [BrainpoolCurveFactory::p320r1(), BrainpoolCurveFactory::p320r1TwistToP320t1(), BrainpoolCurveFactory::p320t1()],
            [BrainpoolCurveFactory::p384r1(), BrainpoolCurveFactory::p384r1TwistToP384t1(), BrainpoolCurveFactory::p384t1()],
            [BrainpoolCurveFactory::p512r1(), BrainpoolCurveFactory::p512r1TwistToP512t1(), BrainpoolCurveFactory::p512t1()]
        ];
    }

    /**
     * @dataProvider provideTwistedCurves
     */
    public function testTwistConsistency(Curve $source, QuadraticTwist $twist, Curve $target): void
    {
        $twister = new QuadraticTwister($source, $twist);
        $actualTarget = $twister->twistCurve();

        $this->assertEquals(0, gmp_cmp($actualTarget->getA(), $target->getA()));
        $this->assertEquals(0, gmp_cmp($actualTarget->getB(), $target->getB()));
        $this->assertTrue($actualTarget->getG()->equals($target->getG()));
    }
}

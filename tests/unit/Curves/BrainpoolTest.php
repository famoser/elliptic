<?php

namespace Famoser\Elliptic\Tests\Curves;

use Famoser\Elliptic\Curves\BrainpoolCurveFactory;
use Famoser\Elliptic\Curves\CurveRepository;
use Famoser\Elliptic\Integration\WycheProof\FixturesRepository;
use Famoser\Elliptic\Math\Twister\QuadraticTwister;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\QuadraticTwist;
use PHPUnit\Framework\TestCase;

class BrainpoolTest extends TestCase
{
    public static function provideTwistedCurves(): array
    {
        return [
            [BrainpoolCurveFactory::p256r1(), BrainpoolCurveFactory::p256r1TwistToP256t1(), BrainpoolCurveFactory::p256t1()]
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
